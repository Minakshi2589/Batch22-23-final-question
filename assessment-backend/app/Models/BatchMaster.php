<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BatchMaster extends Model
{
    protected $table = 'Batch_Master';
    protected $primaryKey = 'Batch_id';
    public $timestamps = false;
    protected $fillable = ['Batch_Name', 'Batch_Start_Date', 'Batch_strength'];
}
