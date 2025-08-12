import axios, { AxiosInstance, AxiosRequestConfig, AxiosResponse, AxiosError } from 'axios';
import { Task, ApiError, ApiResponse, LoginResponse, User } from '../types/interfaces';

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
      
      // Create a standardized error object
      const apiError: ApiError = {
        status,
        message: data?.message || 'An unexpected error occurred',
        errors: data?.errors,
        timestamp: new Date().toISOString(),
      };
      
      return Promise.reject(apiError);
    } else if (error.request) {
      // The request was made but no response was received
      return Promise.reject({
        status: 0,
        message: 'Network error. Please check your connection.',
      });
    } else {
      // Something happened in setting up the request
      return Promise.reject({
        status: 0,
        message: error.message || 'Request configuration error',
      });
    }
  }
);

// Authentication API
export const AuthApi = {
  async login(email: string, password: string): Promise<LoginResponse> {
    const response = await api.post<ApiResponse<LoginResponse>>('/auth/login', {
      email,
      password,
    });
    return response.data.data;
  },

  async register(
    email: string,
    password: string,
    firstName: string,
    lastName: string
  ): Promise<LoginResponse> {
    const response = await api.post<ApiResponse<LoginResponse>>('/auth/register', {
      email,
      password,
      firstName,
      lastName,
    });
    return response.data.data;
  },

  async socialLogin(provider: 'google' | 'github' | 'microsoft', token: string): Promise<LoginResponse> {
    const response = await api.post<ApiResponse<LoginResponse>>('/auth/social', {
      provider,
      token,
    });
    return response.data.data;
  },

  async logout(): Promise<void> {
    await api.post('/auth/logout');
    localStorage.removeItem('authToken');
  },

  async refreshToken(): Promise<string> {
    const response = await api.post<ApiResponse<{ token: string }>>('/auth/refresh');
    return response.data.data.token;
  },

  async getCurrentUser(): Promise<User> {
    const response = await api.get<ApiResponse<User>>('/auth/me');
    return response.data.data;
  },
};

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
  },

  async search(query: string): Promise<Task[]> {
    const response = await api.get<ApiResponse<Task[]>>(`/tasks/search?q=${encodeURIComponent(query)}`);
    return response.data.data;
  },

  async toggleComplete(id: number): Promise<Task> {
    const response = await api.patch<ApiResponse<Task>>(`/tasks/${id}/toggle-complete`);
    return response.data.data;
  },
};

// Statistics API
export const StatsApi = {
  async getTaskStats(): Promise<{
    total: number;
    completed: number;
    pending: number;
    byDay: { date: string; count: number }[];
  }> {
    const response = await api.get<ApiResponse<any>>('/stats/tasks');
    return response.data.data;
  },
};

// Utility function for handling API errors in components
export function handleApiError(error: unknown, defaultMessage = 'An error occurred'): string {
  if (typeof error === 'object' && error !== null) {
    const apiError = error as ApiError;
    if (apiError.message) return apiError.message;
    
    if (apiError.errors) {
      // Format validation errors
      return Object.values(apiError.errors)
        .flat()
        .join(', ');
    }
  }
  
  return defaultMessage;
}

// Refresh token logic (runs periodically)
let refreshTimeout: number | null = null;

export function startTokenRefresh() {
  const refreshToken = async () => {
    try {
      const newToken = await AuthApi.refreshToken();
      localStorage.setItem('authToken', newToken);
      
      // Schedule next refresh 5 minutes before token expiration
      if (refreshTimeout) clearTimeout(refreshTimeout);
      refreshTimeout = window.setTimeout(refreshToken, 55 * 60 * 1000); // 55 minutes
    } catch (error) {
      console.error('Token refresh failed:', error);
      // Try again in 1 minute if failed
      refreshTimeout = window.setTimeout(refreshToken, 60 * 1000);
    }
  };
  
  // Start the refresh cycle
  refreshToken();
}

// Initialize token refresh when token exists
if (localStorage.getItem('authToken')) {
  startTokenRefresh();
}
