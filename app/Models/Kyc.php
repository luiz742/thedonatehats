<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Kyc extends Model
{
    use HasFactory;

    protected $table = 'kyc';

    protected $fillable = [
        'user_id',
        'full_name',
        'date_of_birth',
        'document_type',
        'document_number',
        'document_image',
        'selfie_image',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Verifica se o KYC já foi aprovado
    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    // Atualiza a imagem do documento
    public function updateDocumentImage($file)
    {
        if ($this->document_image) {
            Storage::disk('public')->delete($this->document_image);
        }
        $this->document_image = $file->store('kyc/documents', 'public');
        $this->save();
    }

    // Atualiza a selfie
    public function updateSelfieImage($file)
    {
        if ($this->selfie_image) {
            Storage::disk('public')->delete($this->selfie_image);
        }
        $this->selfie_image = $file->store('kyc/selfies', 'public');
        $this->save();
    }

    // Método para atualizar ou criar um KYC
    public static function updateOrCreateKyc($userId, $data)
    {
        $kyc = self::where('user_id', $userId)->first();

        if ($kyc) {
            if ($kyc->isApproved()) {
                return null; // Impede edição se já foi aprovado
            }

            // Se já existe, apenas atualiza e mantém "pending"
            $kyc->update(array_merge($data, ['status' => 'pending']));
        } else {
            // Primeira criação já começa como "pending"
            $kyc = self::create(array_merge($data, [
                'user_id' => $userId,
                'status' => 'pending'
            ]));
        }

        return $kyc;
    }
}
