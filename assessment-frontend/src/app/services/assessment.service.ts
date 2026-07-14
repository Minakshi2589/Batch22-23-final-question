import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { Batch, Technology, Employee, ReportRow } from '../models/assessment.model';

@Injectable({ providedIn: 'root' })
export class AssessmentService {
  private baseUrl = 'http://localhost:8000/api';

  constructor(private http: HttpClient) {}

  getBatches(): Observable<Batch[]> {
    return this.http.get<Batch[]>(`${this.baseUrl}/batches`);
  }

  getTechnologies(): Observable<Technology[]> {
    return this.http.get<Technology[]>(`${this.baseUrl}/technologies`);
  }

  getEmployees(batchId: number, technologyId: number): Observable<Employee[]> {
    return this.http.get<Employee[]>(`${this.baseUrl}/employees`, {
      params: { batch_id: batchId, technology_id: technologyId },
    });
  }

  saveMark(empid: number, mark: number): Observable<any> {
    return this.http.post(`${this.baseUrl}/marks`, { empid, mark });
  }

  getReport(batchId: number): Observable<ReportRow[]> {
    return this.http.get<ReportRow[]>(`${this.baseUrl}/report`, {
      params: { batch_id: batchId },
    });
  }

  downloadReportPdf(batchId: number): Observable<Blob> {
    return this.http.get(`${this.baseUrl}/report/pdf`, {
      params: { batch_id: batchId },
      responseType: 'blob',
    });
  }
}
