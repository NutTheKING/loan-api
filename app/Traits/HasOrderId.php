<?php
namespace App\Traits;

trait HasOrderId
{
    protected static function bootHasOrderId()
    {
        static::creating(function ($model) {
            if (!$model->order_id && property_exists($model, 'orderIdPrefix')) {
                $model->order_id = $model->generateOrderId();
            }
        });
    }
    
    protected function generateOrderId()
    {
        $prefix = $this->orderIdPrefix ?? 'USR';
        $date = now()->format('ymd'); // 241201 for 2024-12-01
        $sequence = $this->getNextSequence($prefix, $date);
        
        return "{$prefix}{$date}{$sequence}";
    }
    
    protected function getNextSequence($prefix, $date)
    {
        $lastRecord = self::where('order_id', 'like', "{$prefix}{$date}%")
            ->orderBy('order_id', 'desc')
            ->first();
            
        if ($lastRecord) {
            $lastSequence = substr($lastRecord->order_id, strlen($prefix) + 6);
            return str_pad((int)$lastSequence + 1, 4, '0', STR_PAD_LEFT);
        }
        
        return '0001';
    }
}