<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Transaction extends Model
{
    use HasFactory;

    protected $table = 'transactions';

    protected $fillable = [
        'student_id',
        'type',
        'kategori',
        'nama',
        'jumlah',
        'tanggal',
        'metode',
        'keterangan',
    ];

    protected $casts = [
        'jumlah' => 'decimal:2',
        'tanggal' => 'date',
    ];

    protected $with = ['files'];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function files(): HasMany
    {
        return $this->hasMany(TransactionFile::class);
    }

    public function scopeIncome($query)
    {
        return $query->where('type', 'income');
    }

    public function scopeExpense($query)
    {
        return $query->where('type', 'expense');
    }

    public const KATEGORI_INCOME = [
        'Iuran Kas',
        'Iuran Komite',
        'Sumbangan',
        'Lainnya',
    ];

    public const KATEGORI_EXPENSE = [
        'Kebutuhan Kelas',
        'Alat Tulis',
        'Kegiatan',
        'Perlengkapan',
        'Lainnya',
    ];

    public static function kategoriList(string $type): array
    {
        return $type === 'income' ? self::KATEGORI_INCOME : self::KATEGORI_EXPENSE;
    }
}