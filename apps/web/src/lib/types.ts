export type Attendance = {
  id: number;
  user_id: number;
  check_in_at: Date;
  check_out_at: Date | null;
  created_at: string;
  updated_at: string;
};
