import axios, { AxiosInstance } from 'axios';
import { Task } from '../types/tasks';

const API: AxiosInstance = axios.create({
  baseURL: import.meta.env.VITE_API_URL || 'http://localhost:8000/api',
  headers: {
    'Content-Type' : 'application/json',
  },
});

// adding auth tokens to requests
API.interceptors.request.use((config) => {
  const token = localStorage.getItem('authToken');
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  return config;
});

export const TaskService = {
  async getAll(): Promise<Task[]> {
    const response = await API.get('/tasks');
    return response.data.data;
  },

  async create(task: Omit<Task, 'id' | 'user_id' | 'created_at' | 'updated_at'>): Promise<Task> {
    const response = await API.post('/tasks', task);
    return {...task, id: response.data.id};
  },

  async update(id: number, task: Partial<Task>) : Promise<Task> {
    const response = await API.put(`/tasks/${id}`, task);
    return response.data;
  },

  async delete(id:number) : Promise<void> {
    await API.delete(`/tasks/${id}`);
  },
};

export const AuthService = {
  async login(email: string, password: string): Promise<string> {
    const response = await API.post('/auth/login', {email, password});
    return response.data.token;
  },

  async register(email: string, password: string): Promise<string> {
    const response = await API.post('/auth/register', {email, password});
    return response.data.token;
  }
}
