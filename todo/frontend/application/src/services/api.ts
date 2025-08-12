import axios, { AxiosInstance, AxiosRequestConfig, AxiosResponse, AxiosError } from 'axios';
import { Task, ApiResponse, ApiError} from '../types/interfaces';

// Create axios instance with base configuration
const api: AxiosInstance = axios.create({
  baseURL: import.meta.env.VITE_API_URL || 'http://localhost:8000/api',
  timeout: 10000,
  headers: {
    'Content-Type': 'application/json',
  },
});

// Request interceptor to add auth token
api.interceptors.request.use((config: AxiosRequestConfig) => {
  const token = localStorage.getItem('authToken');
  if (token && config.headers) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  return config;
});

// Response interceptor for error handling
api.interceptors.response.use(
  (response: AxiosResponse) => response,
  (error: AxiosError<ApiError>) => {
    if (error.response) {
      // Handle specific status codes
      const { status, data } = error.response;
      
      if (status === 401) {
        // Token expired or invalid
        localStorage.removeItem('authToken');
        window.location.href = '/login';
      }
    }
  }
)

// Tasks API
export const TaskApi = {
  async getAll(): Promise<Task[]> {
    const response = await api.get<ApiResponse<Task[]>>('/tasks');
    return response.data.data;
  },

  async getById(id: number): Promise<Task> {
    const response = await api.get<ApiResponse<Task>>(`/tasks/${id}`);
    return response.data.data;
  },

  async create(task: Omit<Task, 'id' | 'user_id' | 'created_at' | 'updated_at'>): Promise<Task> {
    const response = await api.post<ApiResponse<Task>>('/tasks', task);
    return response.data.data;
  },

  async update(id: number, task: Partial<Task>): Promise<Task> {
    const response = await api.put<ApiResponse<Task>>(`/tasks/${id}`, task);
    return response.data.data;
  },

  async delete(id: number): Promise<void> {
    await api.delete(`/tasks/${id}`);
  }
};