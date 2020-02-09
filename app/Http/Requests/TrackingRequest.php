<?php
declare(strict_types=1);

namespace App\Http\Requests;


class TrackingRequest extends FormRequest
{
  /**
   * Determine if the user is authorized to make this request.
   *
   * @return bool
   */
  public function authorize()
  {
    return true;
  }

  /**
   * Get the validation rules that apply to the request.
   *
   * @return array
   */
  public function rules(): array
  {
    return [
      'revenue' => 'required|gt:0',
      'customerId' => 'required|gt:0|exists:customers,id',
      'bookingNumber' => 'required',
    ];
  }
}
