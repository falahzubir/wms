@props(['filter_data'])
<div class="">
    <h2 class="" id="panelsStayOpen-headingOne">
        <button class="btn btn-link" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapseOne"
            aria-expanded="true" aria-controls="panelsStayOpen-collapseOne">
            <strong>Advance Filter <i class="ri-arrow-down-s-fill"></i></strong>
        </button>
    </h2>
    {{-- {{ (string)$filter_data/ }} --}}
    <div id="panelsStayOpen-collapseOne" class="accordion-collapse collapse
        {{ request('companies') != null || request('couriers') != null ||
            request('purchase_types') != null || request('customer_types') != null ||
            request('products') != null || request('op_models') != null || request('states') != null || request('platforms') != null || request('statuses') != null || request('events') !== null  ? 'show' : '' }}"
        aria-labelledby="panelsStayOpen-headingOne">
        <div class="accordion-body">
            <div class="expand row">
                @isset($filter_data->companies)
                    <x-filter_select name="companies" label="Company(s)" id="company-filter" class="col-4 mt-2">
                        @foreach ($filter_data->companies as $company)
                            <option value="{{ $company->id }}"
                                {{ request('companies') != null ? (in_array($company->id, request('companies')) ? 'selected' : '') : '' }}>
                                {{ $company->name }}</option>
                        @endforeach
                    </x-filter_select>
                @endisset
                @isset($filter_data->couriers)
                    <x-filter_select name="couriers" label="Courier(s)" id="courier-filter" class="col-4 mt-2">
                        @foreach ($filter_data->couriers as $courier)
                            <option value="{{ $courier->id }}"
                                {{ request('couriers') != null ? (in_array($courier->id, request('couriers')) ? 'selected' : '') : '' }}>
                                {{ $courier->name }}</option>
                        @endforeach
                    </x-filter_select>
                @endisset
                @isset($filter_data->states)
                    <x-filter_select name="states" label="State(s)" id="state-filter" class="col-4 mt-2">
                        @foreach ($filter_data->states as $id => $name)
                            <option value="{{ $id }}"
                                {{ request('states') != null ? (in_array($id, request('states')) ? 'selected' : '') : '' }}>
                                {{ $name }}</option>
                        @endforeach
                    </x-filter_select>
                @endisset
                @isset($filter_data->purchase_types)
                    <x-filter_select name="purchase_types" label="Purchase Type(s)" id="purchase-type-filter"
                        class="col-4 mt-2">
                        @foreach ($filter_data->purchase_types as $id => $name)
                            <option value="{{ $id }}"
                                {{ request('purchase_types') != null ? (in_array($id, request('purchase_types')) ? 'selected' : '') : '' }}>
                                {{ $name }}</option>
                        @endforeach
                    </x-filter_select>
                @endisset
                @isset($filter_data->teams)
                    <x-filter_select name="teams" label="Team(s)" id="team-filter" class="col-4 mt-2" />
                @endisset
                @isset($filter_data->customer_types)
                    <x-filter_select name="customer_types" label="Customer Type(s)" id="customer-type-filter"
                        class="col-4 mt-2">
                        @foreach ($filter_data->customer_types as $id => $name)
                            <option value="{{ $id }}"
                                {{ request('customer_types') != null ? (in_array($id, request('customer_types')) ? 'selected' : '') : '' }}>
                                {{ $name }}</option>
                        @endforeach
                    </x-filter_select>
                @endisset
                @isset($filter_data->products)
                    <x-filter_select name="products" label="Product(s)" id="product-filter" class="col-4 mt-2">
                        @foreach ($filter_data->products as $product)
                            <option value="{{ $product->id }}"
                                {{ request('products') != null ? (in_array($product->id, request('products')) ? 'selected' : '') : '' }}>
                                {{ $product->name }}</option>
                        @endforeach
                    </x-filter_select>
                    <x-filter_select name="not_products" label="Exclude Product(s)" id="exclude-product-filter" class="col-4 mt-2">
                        @foreach ($filter_data->products as $product)
                            <option value="{{ $product->id }}"
                                {{ request('not_products') != null ? (in_array($product->id, request('not_products')) ? 'selected' : '') : '' }}>
                                {{ $product->name }}</option>
                        @endforeach
                    </x-filter_select>
                @endisset

                @isset($filter_data->operational_models)
                    <x-filter_select name="op_models" label="Operational Model(s)" id="operational-model-filter" class="col-4 mt-2">
                        @foreach($filter_data->operational_models as $opm)
                            <option value="{{$opm->id}}" {{ request('op_models') != null ? (in_array($opm->id, request('op_models')) ? 'selected' : '') : '' }}>{{$opm->name}}</option>
                        @endforeach
                    </x-filter_select>
                @endisset
                @isset($filter_data->sale_events)
                    <x-filter_select name="events" label="Sales Event" id="sales-event-filter" class="col-4 mt-2">
                        @foreach ($filter_data->sale_events as $event)
                            <option value="{{ $event['event_id'].'|'.$event['company_id'] }}"
                                {{ request('events') != null ? (in_array($event['event_id'].'|'.$event['company_id'], request('events')) ? 'selected' : '') : '' }}>
                                {{ $event['event_name'] }}</option>
                        @endforeach
                    </x-filter_select>
                @endisset
                @isset($filter_data->platforms)
                    <x-filter_select name="platforms" label="Platform" id="platform-filter" class="col-4 mt-2">
                        @foreach ($filter_data->platforms as $id => $name)
                            <option value="{{ $id }}"
                                {{ request('platforms') != null ? (in_array($id, request('platforms')) ? 'selected' : '') : '' }}>
                                {{ $name }}</option>
                        @endforeach
                    </x-filter_select>
                @endisset
                @isset($filter_data->statuses)
                    <x-filter_select name="statuses" label="Status" id="status-filter" class="col-4 mt-2">
                        @foreach ($filter_data->statuses as $id => $name)
                            <option value="{{ $id }}"
                                {{ request('statuses') != null ? (in_array($id, request('statuses')) ? 'selected' : '') : '' }}>
                                {{ $name }}
                            </option>
                        @endforeach
                    </x-filter_select>
                @endisset
            </div>
        </div>
    </div>
</div>
