export interface Batch {
  Batch_id: number;
  Batch_Name: string;
}

export interface Technology {
  Technology_id: number;
  Technology_Name: string;
}

export interface Employee {
  Employee_id: number;
  Employee_Name: string;
}

export interface ReportRow {
  slno: number;
  Batch_Name: string;
  Technology_Name: string;
  Employee_Name: string;
  Employee_Phone: string;
  mark: number;
  grade: string;
  status: string;
}
