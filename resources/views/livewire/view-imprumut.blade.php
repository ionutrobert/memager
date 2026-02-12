<div>
    {{-- Care about people's approval and you will be their prisoner. --}}

    @foreach ($debts as $debt)
        <div>
            <h2>Fișa împrumutului pentru {{ $debt->member->full_name }}</h2>
            <p>Suma Acordată: <b>{{ $debt->suma }} Lei</b></p>

            <p>Data Acordării: <b>{{ $debt->data_acordare }}</b></p>
            <p>Procent Dobândă: <b>{{ $debt->procent }} %</b></p>

            @php
                // INITIALIZING AS THERE IS NO PREVIEWS PAYMENT DATE
                $prevDate = $debt->data_acordare;
                // INITIALIZING AS THERE IS NO PREVIEWS SOLD RAMAS
                $soldRamasRata = $debt->suma;
                $soldRamasDobanda = 0;

            @endphp
        </div>
        <div class="mt-4 overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-500">
                <thead class="bg-gray-50 text-xs uppercase text-gray-700">
                    <tr>
                        <th class="px-6 py-3">Data</th>
                        <th class="px-6 py-3">Plata Suma</th>
                        <th class="px-6 py-3">Zile</th>
                        <th class="px-6 py-3">Dobanda Calc</th>
                        <th class="px-6 py-3">Sold Imprumut</th>
                        <th class="px-6 py-3">Sold Dobanda</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($debt->payment as $payment)
                        <tr class="border-b">
                            <td class="px-6 py-3">{{ $payment->data }}</td>
                            <td class="px-6 py-3">{{ $payment->suma }} lei</td>

                            <td class="px-6 py-3">{{ $days = $this->calculateDays($payment->data, $prevDate) }}</td>
                            @php
                                $dobandaCalculata = $this->calculateInterest($payment, $days, $soldRamasRata);

                                $soldRamasDobanda = $soldRamasDobanda + $dobandaCalculata - $payment->suma;

                                // IF SOLD RAMAS DOBANDA IS NEGATIVE, SUBSTRACT FROM RATA
                                if ($soldRamasDobanda < 0) {
                                    $soldRamasRata = $soldRamasRata + $soldRamasDobanda;
                                    $soldRamasDobanda = 0;
                                }
                            @endphp

                            <td class="px-6 py-3">{{ $dobandaCalculata }} Lei
                            </td>

                            <td class="px-6 py-3">{{ $soldRamasRata }} Lei</td>
                            <td class="px-6 py-3">{{ $soldRamasDobanda }} Lei</td>
                        </tr>

                        @php
                            // SAVING CURRENT DATE AS PREV DATE AFTER DISPLAYING AT THE END OF THE LOOP
                            $prevDate = $payment->data;

                        @endphp
                    @endforeach

                    <!-- Current Date Summary Row -->
                    @php
                        $todaySummary = $this->calculateTodaysSummary(
                            $debt,
                            $prevDate,
                            $soldRamasRata,
                            $soldRamasDobanda,
                        );
                    @endphp
                    <tr class="border-b bg-blue-50 font-semibold">
                        <td class="px-6 py-3">{{ $todaySummary['date'] }}</td>
                        <td class="px-6 py-3">Zi curenta</td>
                        <td class="px-6 py-3">{{ $todaySummary['days'] }}</td>
                        <td class="px-6 py-3">{{ $todaySummary['interest'] }} Lei</td>
                        <td class="px-6 py-3">{{ $todaySummary['soldRamas'] }} Lei</td>
                        <td class="px-6 py-3">{{ $todaySummary['soldRamasDobanda'] }} Lei</td>
                    </tr>
                </tbody>
            </table>
        </div>
    @endforeach

</div>
