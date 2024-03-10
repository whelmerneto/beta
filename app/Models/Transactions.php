<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transactions extends Model
{
    use HasFactory;

    protected $fillable = ["sender_id", "receiver_id", "amount", "scheduled", "schedule_date", "sent"];
    protected $table = "transactions";
    protected $connection = "mysql";
}
