<div class="w-full max-w-[85rem] py-10 px-4 sm:px-6 lg:px-8 mx-auto">
    <h1 class="text-4xl font-bold text-slate-500">Мои заказы</h1>

    <!-- Grid -->
    <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mt-5">
        <!-- Card -->
        <div class="flex flex-col bg-white border shadow-sm rounded-xl dark:bg-slate-900 dark:border-gray-800">
            <div class="p-4 md:p-5 flex gap-x-4">
                <div class="flex-shrink-0 flex justify-center items-center size-[46px] bg-gray-100 rounded-lg dark:bg-gray-800">
                    <svg class="flex-shrink-0 size-5 text-gray-600 dark:text-gray-400" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                        <circle cx="9" cy="7" r="4" />
                        <path d="M22 21v-2a4 4 0 0 0-3-3.87" />
                        <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                    </svg>
                </div>

                <div class="grow">
                    <div class="flex items-center gap-x-2">
                        <p class="text-xs uppercase tracking-wide text-gray-500">
                            Клиент
                        </p>
                    </div>
                    <div class="mt-1 flex items-center gap-x-2">
                        <div>{{$address->getFullAddressAttribute()}}</div>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Card -->

        <!-- Card -->
        <div class="flex flex-col bg-white border shadow-sm rounded-xl dark:bg-slate-900 dark:border-gray-800">
            <div class="p-4 md:p-5 flex gap-x-4">
                <div class="flex-shrink-0 flex justify-center items-center size-[46px] bg-gray-100 rounded-lg dark:bg-gray-800">
                    <svg class="flex-shrink-0 size-5 text-gray-600 dark:text-gray-400" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M5 22h14" />
                        <path d="M5 2h14" />
                        <path d="M17 22v-4.172a2 2 0 0 0-.586-1.414L12 12l-4.414 4.414A2 2 0 0 0 7 17.828V22" />
                        <path d="M7 2v4.172a2 2 0 0 0 .586 1.414L12 12l4.414-4.414A2 2 0 0 0 17 6.172V2" />
                    </svg>
                </div>

                <div class="grow">
                    <div class="flex items-center gap-x-2">
                        <p class="text-xs uppercase tracking-wide text-gray-500">
                            Дата заказа
                        </p>
                    </div>
                    <div class="mt-1 flex items-center gap-x-2">
                        <h3 class="text-xl font-medium text-gray-800 dark:text-gray-200">
                            {{$order_items[0]->created_at->format('d-m-Y')}}
                        </h3>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Card -->

        <!-- Card -->
        <div class="flex flex-col bg-white border shadow-sm rounded-xl dark:bg-slate-900 dark:border-gray-800">
            <div class="p-4 md:p-5 flex gap-x-4">
                <div class="flex-shrink-0 flex justify-center items-center size-[46px] bg-gray-100 rounded-lg dark:bg-gray-800">
                    <svg class="flex-shrink-0 size-5 text-gray-600 dark:text-gray-400" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 11V5a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h6" />
                        <path d="m12 12 4 10 1.7-4.3L22 16Z" />
                    </svg>
                </div>

                <div class="grow">
                    <div class="flex items-center gap-x-2">
                        <p class="text-xs uppercase tracking-wide text-gray-500">
                            Статус
                        </p>
                    </div>
                    <div class="mt-1 flex items-center gap-x-2">
                        <span class="bg-yellow-500 py-1 px-3 rounded text-white shadow">{{$order->status == 'new'? 'В обработке':'Отправлен'}}</span>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Card -->

        <!-- Card -->

        <!-- End Card -->
    </div>
    <!-- End Grid -->

    <div class="flex flex-col md:flex-row gap-4 mt-4">
        <div class="md:w-3/4">
            <div class="bg-white overflow-x-auto rounded-lg shadow-md p-6 mb-4">
                <table class="w-full">
                    <thead>
                    <tr>
                        <th class="text-left font-semibold">Продукт</th>
                        <th class="text-left font-semibold">Цена</th>
                        <th class="text-left font-semibold">Кол-во</th>
                        <th class="text-left font-semibold">Итого</th>
                    </tr>
                    </thead>
                    <tbody>

                    <!--[if BLOCK]><![endif]-->

                    @foreach($order_items as $item)
                        <tr wire:key="{{$item->id}}">
                            <td class="py-4">
                                <div class="flex items-center">
                                    <img class="h-16 w-16 mr-4" src="{{url('storage', $item->product->images[0])}}" alt="{{$item->product->name}}">
                                    <span class="font-semibold">{{$item->product->name}}</span>
                                </div>
                            </td>
                            <td class="py-4">{{Number::currency($item->unit_amount, 'RUB')}}</td>
                            <td class="py-4">
                                <span class="text-center w-8">{{$item->quantity}}</span>
                            </td>
                            <td class="py-4">{{Number::currency($item->total_amount, 'RUB')}}</td>
                        </tr>
                    @endforeach



                    </tbody>
                </table>
            </div>

            <div class="bg-white overflow-x-auto rounded-lg shadow-md p-6 mb-4">
                <h1 class="font-3xl font-bold text-slate-500 mb-3">Адрес доставки</h1>
                <div class="flex justify-between items-center">
                    <div>
                        <p>{{$address->street_address . ', ' . $address->city . ', ' . $address->state . ', ' . $address->zip_code}}</p>
                    </div>
                    <div>
                        <p class="font-semibold">Телефон:</p>
                        <p>{{$address->phone}}</p>
                    </div>
                </div>
            </div>

        </div>
        <div class="md:w-1/4">
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-lg font-semibold mb-4">Счет</h2>
                <div class="flex justify-between mb-2">
                    <span>Налоги</span>
                    <span>{{Number::currency(0, 'RUB')}}</span>
                </div>
                <div class="flex justify-between mb-2">
                    <span>Доставка</span>
                    <span>{{Number::currency(0, 'RUB')}}</span>
                </div>
                <hr class="my-2">
                <div class="flex justify-between mb-2">
                    <span class="font-semibold">Итого</span>
                    <span class="font-semibold">{{Number::currency($order->grand_total, 'RUB')}}</span>
                </div>

            </div>
        </div>
    </div>
</div>
