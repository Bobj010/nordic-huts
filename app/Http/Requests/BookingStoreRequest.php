<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BookingStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'house_id' => ['required', 'integer', 'exists:houses,id'],
            'check_in_date' => ['required', 'date', 'after_or_equal:today'],
            'check_out_date' => ['required', 'date', 'after:check_in_date'],
            'guests_count' => ['required', 'integer', 'min:1', 'max:20'],
            'guest_name' => ['required', 'string', 'max:255'],
            'guest_phone' => ['required', 'string', 'max:50'],
            'comment' => ['nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * Custom validation messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'house_id.required' => 'Не выбран домик для бронирования.',
            'house_id.exists' => 'Выбранный домик не найден в системе.',
            'check_in_date.required' => 'Укажите дату заезда.',
            'check_in_date.date' => 'Некорректный формат даты заезда.',
            'check_in_date.after_or_equal' => 'Дата заезда не может быть в прошлом.',
            'check_out_date.required' => 'Укажите дату выезда.',
            'check_out_date.date' => 'Некорректный формат даты выезда.',
            'check_out_date.after' => 'Дата выезда должна быть строго позже даты заезда.',
            'guests_count.required' => 'Укажите количество гостей.',
            'guests_count.min' => 'Количество гостей должно быть не менее 1 человека.',
            'guests_count.max' => 'Превышено максимальное число гостей.',
            'guest_name.required' => 'Укажите контактное имя гостя.',
            'guest_phone.required' => 'Укажите контактный номер телефона для подтверждения брони.',
            'comment.max' => 'Комментарий не может превышать 1000 символов.',
        ];
    }
}
