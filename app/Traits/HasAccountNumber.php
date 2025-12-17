<?php
// App/Traits/HasAccountNumber.php
namespace App\Traits;

trait HasAccountNumber
{
    protected static function bootHasAccountNumber()
    {
        static::creating(function ($model) {
            if (empty($model->account_num)) {
                $model->account_num = $model->generateAccountNumber();
            }
        });
    }
    
    public function generateAccountNumber()
    {
        // Custom logic per model
        if (method_exists($this, 'accountNumberFormat')) {
            return $this->accountNumberFormat();
        }
        
        // Default format
        $prefix = strtoupper(substr(class_basename($this), 0, 3));
        $timestamp = now()->format('YmdHis');
        $random = mt_rand(100, 999);
        
        return $prefix . $timestamp . $random;
    }
}