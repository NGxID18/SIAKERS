<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    use HasFactory;

    protected $table = 'activity_logs';

    protected $fillable = [
        'user_role',
        'seksi_name',
        'action',
        'description',
        'ip_address',
    ];

    /**
     * Helper statis untuk mencatat log aktivitas pengguna.
     */
    public static function record(string $action, string $description): self
    {
        $roleName = 'Seksi Operasional';
        if (session('is_admin')) {
            $roleName = 'Admin System';
        } elseif (session('user_role') === 'tata_usaha') {
            $roleName = 'Tata Usaha RS';
        }

        $seksiObj = Seksi::find(session('user_seksi_id', 1));
        $seksiName = $seksiObj ? $seksiObj->nama_seksi : 'RS Central';

        return self::create([
            'user_role' => $roleName,
            'seksi_name' => $seksiName,
            'action' => $action,
            'description' => $description,
            'ip_address' => request()->ip() ?? '127.0.0.1',
        ]);
    }
}
