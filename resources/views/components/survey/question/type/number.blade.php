<label class="block">
    <p class="text-lg leading-6 font-medium text-gray-900 mb-0.5">{!! $question->title !!} {!! in_array('required', $question->rules) ? '<span class="text-red-600 text-base">*</span>' : '' !!}</p>
    <p class="text-sm text-gray-500 mb-2">{!! $question->content !!}</p>
    <input type="number" name="{{ $question->key }}" id="{{ $question->key }}" value="{{ old($question->key) }}" {{ in_array('required', $question->rules) ? 'required' : '' }}
           class="block w-full rounded-md @error($question->key) border-red-600 @else border-gray-300 @enderror shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
    @error($question->key)
    <p class="text-sm text-red-700 mt-1">{{ $message }}</p>
    @enderror
</label>
