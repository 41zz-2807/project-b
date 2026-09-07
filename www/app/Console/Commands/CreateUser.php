<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Database\QueryException;

class CreateUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:create {name?} {email?} {password?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Buat pengguna baru (email sudah ada akan diperbarui)';

    public function handle(): int
    {
        $name = $this->argument('name') ?? $this->ask('Nama');
        $email = $this->argument('email') ?? $this->ask('Email');
        $password = $this->argument('password') ?? $this->secret('Password');

        if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            $this->error('Email tidak valid.');

            return self::FAILURE;
        }

        if (mb_strlen((string) $password) < 6) {
            $this->error('Password minimal 6 karakter.');

            return self::FAILURE;
        }

        try {
            $user = User::updateOrCreate(
                ['email' => $email],
                ['name' => $name, 'password' => $password]
            );
        } catch (QueryException $e) {
            $this->error('Gagal menyimpan pengguna: '.$e->getMessage());

            return self::FAILURE;
        }

        $this->info("Pengguna '{$user->name}' ({$user->email}) siap digunakan.");

        return self::SUCCESS;
    }
}
