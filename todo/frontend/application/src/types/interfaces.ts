export interface Task {
  id: number;
  user_id: number;
  title: string;
  completed: boolean;
  description?: string;
  createdAt?: string;
  updatedAt: string;
}

export interface ApiError {
  status: number;
  message: string;
  errors?: Record<string, string[]>;
  timestamp?: string;
}

export interface ApiResponse<T> {
  success: boolean;
  data: T;
}