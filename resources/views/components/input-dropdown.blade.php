@props([
    'items', 'selected', 'id', 'label','class', 'name', 'multiple','hints'
])



@php
    $items = is_array($items ?? []) ? collect($items ?? []) : ($items ?? collect([]));
    $class =( $class ?? '').' w-full sm:min-w-40 md:min-w-64 border rounded-md dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500';

    $selected_ids = $selected ?? [];

    $selected_sub_items  = collect([]);
    $item_has_children  = false;

    if( $multiple ?? false ){

        $_selected = $items->reduce(function($items, $item) use($selected_ids){
            if( $item->children ) {
                
                $items = $items->merge(
                    $item->children->filter(function($child) use($selected_ids, &$selected_sub_items){
                        return in_array( $child->id, $selected_ids ); 
                    })
                );
                    
            } else {
                if( in_array( $item->id, $selected_ids ) ) {
                    $items->push($item);
                }
            }
                
            return $items;
                
        }, collect([]));
            
    } else {
        $_selected = $items->where('id', $selected)->first();
    }

    $id = $id ?? uniqid();


    $is_multiple = $multiple ?? false;

    $is_selected = function( $id ) use( $is_multiple, $selected_ids){
        if($is_multiple) {
            return in_array( $id, $selected_ids);
        }
        return $id == $selected_ids;
    };

    $unique_id = $id;

    $preview_item_class = 'px-2 py-0.5 dark:bg-white dark:text-gray-800 font-semibold border rounded-lg inline-block';
    $preview_item_remove_class = 'border h-6 w-6 rounded-full ml-2 bg-yellow-400 hover:bg-rose-700 hover:text-white';

@endphp


<fieldset class="{{ $class }}" id="{{ $unique_id }}">
    @if(isset($label))
        <legend class="font-semibold mx-4 text-sm">{{ $label }}</legend>
    @endif

    @if(isset($hints))
        <div class="mx-3 text-gray-500 dark:text-gray-400 text-sm mt-1 mb-1">{{ $hints }}</div>
    @endif
    <div class="relative px-2 pb-2">
        <div 
            id="{{ 'dropdown'.$id.'Button' }}" 
            data-dropdown-toggle="{{ 'dropdown'.$id }}" 
            data-dropdown-placement="bottom" 
            class="border dark:border-gray-600 px-2 text-sm font-medium rounded-lg text-center flex items-center w-full min-h-8" 
            type="button"
        >
            
        
            @if($multiple ?? false)
                <div class="rounded-md flex-grow text-left line-clamp-2 preview flex gap-2 flex-wrap py-2">
                    @foreach( $_selected as $item )
                        <div class="{{$preview_item_class}}" data-item-id="{{$item->id}}">
                            <span> {{ isset($item->id) ? $item->id . " - ":'' }} {{$item->name}}</span>
                            <button class="{{ $preview_item_remove_class }}">&times</button>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="rounded-md flex-grow text-left line-clamp-2 preview flex gap-2 flex-wrap">
                    @if($_selected)
                        <span class="leading-none"> {{ isset($_selected->id) ? $_selected->id . " - ":'' }} {{$_selected->name}}</span>
                    @endif
                </div>
            @endif
            

            <svg class="w-2.5 h-2.5 ms-3 flex-shrink-0" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4"/>
            </svg>


        </div> 

        @if($name ?? '')
            @error($name)
                <div class="text-red-500 text-sm">{{ $message }}</div>
            @enderror
        @endif

        <div id="{{ 'dropdown'.$id }}" class="z-10 hidden bg-white rounded-lg shadow  dark:bg-gray-700 left-0 right-0 w-full">
            <div class="min-h-[50px] max-h-[200px] overflow-y-auto w-full border rounded-md">

                <ul class="border rounded-md list">
                    @forelse ( $items as $item)
                        @if($item->children)
                            <li class="border-b border-gray-400 sub-list">

                                <div class="px-2 py-1  dark:bg-gray-800 pb-2 border-b font-bold">{{ $item->id }} - {{  $item->name }}</div>

                                <ul class="ml-4 grid lg:grid-cols-4">
                                    @foreach ($item->children as $child)
                                        <li class="list-item border-b flex items-center dark:border-gray-600  {{ $is_selected($child->id) ? 'text-gray-700 dark:text-gray-300 dark:bg-blue-800':'text-gray-900  dark:text-gray-300' }}">
                                            <label class="flex items-center px-2 py-2  w-full cursor-pointer">
                                                <input 
                                                    data-id="{{ $child->id }}"
                                                    type="{{ ( $multiple ?? false ) ? 'checkbox':'radio' }}" 
                                                    name="{{ $name ?? '' }}" 
                                                    {{ $is_selected($child->id) ? 'checked':'' }} 
                                                    value="{{ $child->id }}"    
                                                    class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"
                                                >
                                                <div class="ms-2 text-sm font-medium label">
                                                    {{$child->id}} - {{$child->name}}
                                                </div>
                                            </label>
                                        </li>
                                    @endforeach
                                </ul>
                                
                            </li>

                        @else

                            <li class="list-item border-b flex items-center dark:border-gray-600  {{ $is_selected( $item->id ) ? 'text-gray-700 dark:text-gray-300 dark:bg-blue-800':'text-gray-900  dark:text-gray-300' }}">
                                <label class="flex items-center px-2 py-2  w-full cursor-pointer">
                                    <input 
                                        data-id="{{ $item->id }}"
                                        type="{{ ( $multiple ?? false ) ? 'checkbox':'radio' }}" 
                                        name="{{ $name ?? '' }}" {{ $is_selected( $item->id ) ? 'checked':'' }} 
                                        value="{{ $item->id }}"    
                                        class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"
                                    >
                                    <div class="ms-2 text-sm font-medium label">
                                        {{$item->id}} - {{$item->name}}
                                    </div>
                                </label>
                            </li>
                        @endif
                    @empty
                        <li class="border-b flex items-center">
                            <span>--No Data Found--</span>
                        </li>
                    @endforelse
                    
                </ul>
            </div>
        </div>

        
    </div>
  
</fieldset>

<script>

  
all_dropdown_list['{{ $unique_id }}'] = () => {

        
    InputDropdown( () => {
        
        //'items', 'selected', 'id', 'label','class', 'name', 'multiple'
        
        let items = []; 
        try{
            // items = JSON.parse( items );
            items = {!! json_encode($items) !!};
            
            
            items = items.filter(item => item.type==2);
            
            //console.log({items});
            
        }catch( err ){
            items = [];
            console.log(err)
        }
        
        return {
            items,
            id: '{{ $unique_id }}',
            multiple: Boolean( parseInt('{{ $multiple ?? 0 }}')),
            preview_item_class: "{{ $preview_item_class }}",
            preview_item_remove_class: "{{ $preview_item_remove_class }}",
        };
        
    })
}

 


window.addEventListener('load', all_dropdown_list['{{ $unique_id }}'])

</script>