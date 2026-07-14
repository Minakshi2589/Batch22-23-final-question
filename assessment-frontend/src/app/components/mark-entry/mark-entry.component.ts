import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { FormBuilder, FormGroup, ReactiveFormsModule, Validators } from '@angular/forms';
import { AssessmentService } from '../../services/assessment.service';
import { Batch, Technology, Employee, ReportRow } from '../../models/assessment.model';

@Component({
  selector: 'app-mark-entry',
  standalone: true,
  imports: [CommonModule, ReactiveFormsModule, FormsModule],
  templateUrl: './mark-entry.component.html',
  styleUrls: ['./mark-entry.component.scss'],
})
export class MarkEntryComponent implements OnInit {
  entryForm: FormGroup;
  reportBatchId: number | null = null;

  batches: Batch[] = [];
  technologies: Technology[] = [];
  employees: Employee[] = [];
  reportRows: ReportRow[] = [];

  successMessage = '';
  errorMessage = '';

  constructor(private fb: FormBuilder, private assessmentService: AssessmentService) {
    this.entryForm = this.fb.group({
      batch_id: [null, Validators.required],
      technology_id: [null, Validators.required],
      employee_id: [null, Validators.required],
      mark: [null, [Validators.required, Validators.min(0), Validators.max(100)]],
    });
  }

  ngOnInit(): void {
    this.assessmentService.getBatches().subscribe((data) => (this.batches = data));
    this.assessmentService.getTechnologies().subscribe((data) => (this.technologies = data));

    this.entryForm.get('batch_id')!.valueChanges.subscribe(() => this.loadEmployees());
    this.entryForm.get('technology_id')!.valueChanges.subscribe(() => this.loadEmployees());
  }

  loadEmployees(): void {
    const batchId = this.entryForm.value.batch_id;
    const technologyId = this.entryForm.value.technology_id;
    this.employees = [];
    this.entryForm.patchValue({ employee_id: null }, { emitEvent: false });

    if (batchId && technologyId) {
      this.assessmentService.getEmployees(batchId, technologyId).subscribe({
        next: (data) => (this.employees = data),
        error: () => (this.errorMessage = 'Unable to load employees'),
      });
    }
  }

  onSave(): void {
    this.successMessage = '';
    this.errorMessage = '';

    if (this.entryForm.invalid) {
      this.entryForm.markAllAsTouched();
      return;
    }

    const { employee_id, mark } = this.entryForm.value;

    this.assessmentService.saveMark(employee_id, mark).subscribe({
      next: (res) => {
        this.successMessage = res.message;
        this.entryForm.patchValue({ employee_id: null, mark: null });
        this.loadEmployees();
      },
      error: (err) => {
        this.errorMessage = err.error?.message || 'Failed to save mark';
      },
    });
  }

  onShowReport(): void {
    if (!this.reportBatchId) return;
    this.assessmentService.getReport(this.reportBatchId).subscribe({
      next: (data) => (this.reportRows = data),
      error: () => (this.errorMessage = 'Unable to load report'),
    });
  }

  onDownloadPdf(): void {
    if (!this.reportBatchId) return;
    this.assessmentService.downloadReportPdf(this.reportBatchId).subscribe((blob) => {
      const url = window.URL.createObjectURL(blob);
      const a = document.createElement('a');
      a.href = url;
      a.download = 'assessment_report.pdf';
      a.click();
      window.URL.revokeObjectURL(url);
    });
  }
}
