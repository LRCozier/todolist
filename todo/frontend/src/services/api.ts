import axios, { AxiosInstance } from 'axios';
import { Task, ApiResponse, TaskCreatePayload, TaskUpdatePayload, UserCredentials, UserRegistrationPayload, AuthResponse } from '../types/interfaces';

const api: AxiosInstance = axios.create({
  baseURL: import.meta.env.VITE_API_URL || 'http://localhost:8000/api/',
  timeout: 10000,
  withCredentials: true,
  headers: {
    'Content-Type': 'application/json',
  },
});

api.interceptors.request.use(
  (config) => {
    const token = localStorage.getItem('auth_token');
    if (token) {
      config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
  },
  (error) => {
    return Promise.reject(error);
  }
);

api.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error.response?.status === 401) {
      localStorage.removeItem('auth_token'); 
      window.location.href = '/'
    }
    return Promise.reject(error);
  }
);

export const TaskApi = {
  async getAll(): Promise<Task[]> {
    const response = await api.get<ApiResponse<Task[]>>('/tasks');
    return response.data.data;
  },

  async getById(id: number): Promise<Task> {
    const response = await api.get<ApiResponse<Task>>(`/tasks/${id}`);
    return response.data.data;
  },

  async create(task: TaskCreatePayload): Promise<Task> {
    const response = await api.post<ApiResponse<Task>>('/tasks', task);
    return response.data.data;
  },

  async update(id: number, task: TaskUpdatePayload): Promise<Task> {
    const response = await api.put<ApiResponse<Task>>(`/tasks/${id}`, task);
    return response.data.data;
  },

  async delete(id: number): Promise<void> {
    await api.delete(`/tasks/${id}`);
  },
};

export const AuthApi = {
  async login(credentials: UserCredentials): Promise<AuthResponse> {
    const response = await api.post<AuthResponse>('/auth', { action: 'login', ...credentials });
    if (response.data.success && response.data.token){
      localStorage.setItem('auth_token', response.data.token);
    }
    return response.data;
  },

  async register(payload: UserRegistrationPayload): Promise<AuthResponse> {
    const response = await api.post<AuthResponse>('/auth', { action: 'register', ...payload });
    return response.data;
  },

  async logout(): Promise<void> {
    localStorage.removeItem('auth_token');
    try {
      await api.post('/auth', { action: 'logout' });
    } catch (error) {
      console.log('Logout Completed (Service Unavailable)');
    }}
  };

  export const checkAuthStatus = async(): Promise<{isLoggedIn: boolean, user:any}> => {
    const token = localStorage.getItem('auth_token'); 
    if (!token) {
      return {
        isLoggedIn: false,
        user: null
      };
    }

    try {
      await TaskApi.getAll();
      const userData = JSON.parse(localStorage.getItem('user_data') || '{}');
      return {
        isLoggedIn: true,
        user: userData
      }; 
    } catch (error) {
      localStorage.removeItem('auth_token');
      return {
        isLoggedIn: false,
        user: null
      };
    }
    }
