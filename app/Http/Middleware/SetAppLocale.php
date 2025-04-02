<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SetAppLocale
{
    public function handle(Request $request, Closure $next)
    {
        // Получаем язык из сессии (установлен вручную через переключатель в шапке)
        $locale = session('app_locale', config('app.locale'));
        app()->setLocale($locale);

        // Дополнительно: если передан customer_id, сохранить язык клиента в сессию для уведомлений
        if ($request->has('customer_id')) {
            $customer = \App\Models\Customer::find($request->customer_id);
            if ($customer && $customer->preferred_language) {
                session(['customer_locale' => $customer->preferred_language]);
            }
        }

        return $next($request);
    }
}
