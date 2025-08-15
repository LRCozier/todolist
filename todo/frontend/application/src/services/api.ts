import axios, { AxiosInstance} from 'axios';
import { Task, ApiResponse, TaskCreatePayload, TaskUpdatePayload } from '../types/interfaces';

const api: AxiosInstance = axios.create({
  baseURL: import.meta.env.VITE_API_URL || 'http://localhost:3000/', 
  timeout: 10000,
  headers: {
    'Content-Type': 'application/json',
  },
});

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
  }
};