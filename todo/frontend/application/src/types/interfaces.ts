export interface Task {
  id: number;
  user_id: number;
  title: string;
  completed: boolean;
  description?: string;
  createdAt?: string;
  updatedAt: string;
}