export interface Task {
  id: number;
  user_id: number;
  title: string;
  completed: boolean;
  description?: string;
  createdAt?: string;
  updatedAt: string;
}

export interface User {
  id: number;
  email: string;
  createdAt: string;
}

export interface ApiError {
  status: number;
  message: string;
  errors?: Record<string, string[]>;
  timestamp?: string;
}

export interface LoginResponse {
  token: string;
  user: User;
}

export interface ApiResponse<T> {
  success: boolean;
  data: T;
}