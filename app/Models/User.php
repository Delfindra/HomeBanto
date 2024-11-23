<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Exception;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser, MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'diet_id'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Attempt to create a new user and handle duplicate email errors.
     */
    public static function createWithDuplicateCheck(array $data)
    {
        try {
            return self::create($data);
        } catch (QueryException $e) {
            // Check if the exception is due to a duplicate email
            if ($e->getCode() === '23000' && strpos($e->getMessage(), 'users_email_unique') !== false) {
                // Handle the duplicate email case, return a custom error or message
                // Log the exception for debugging
                Log::error('Duplicate email attempted: ' . $data['email']);

                throw new Exception('The email address is already in use.');
            }

            // Rethrow the exception if it's not related to the duplicate email
            throw $e;
        }
    }

    protected static function booted()
    {
        static::created(function (User $user) {
            $user->assignRole('user'); // Replace 'user' with your desired default role
        });
    }

    public function canAccessPanel(Panel $panel): bool
    {
//        if ($panel->getId() === 'admin') {
//            return $this->hasRole( 'admin') && $this->hasVerifiedEmail();
//        }
        return true;
    }

    public function preferences()
    {
        return $this->hasOne(userPreferences::class, 'user_id', 'user_id');
    }

    public function allergies()
    {
        return $this->hasMany(allergies::class);
    }

    public function ingredients()
    {
        return $this->hasMany(Ingredients::class, 'users_id', 'id');
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
