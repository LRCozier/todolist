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

export interface UserCredentials {
  username: string;
  password: string;
}

export interface UserRegistrationPayload {
  username: string;
  email: string;
  password: string;
  confirm_password: string;
}

export interface AuthResponse {
  success: boolean;
  message?: string;
  error?: string;
  user?: {
    id: number;
    username: string;
    email: string;
  };
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
