export interface Task {
  id: number;
  user_id: number;
  title: string;
  completed: boolean;
  description?: string;
  createdAt: string;
  updatedAt: string;
}

export interface TaskCreatePayload {
  title: string;
  description?: string;
}

export interface TaskUpdatePayload {
  title?: string;
  description?: string;
  completed?: boolean;
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