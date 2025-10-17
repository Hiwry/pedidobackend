<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <title>Pedido #{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }} - Costura</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Montserrat', sans-serif;
            font-size: 12px;
            line-height: 1.3;
            padding: 12px;
            margin: 0;
            width: 100%;
            background-color: #ffffff;
        }
        .header {
            text-align: center;
            margin-bottom: 10px;
            border-bottom: 2px solid #4F46E5;
            padding-bottom: 8px;
        }
        .header h1 {
            font-size: 16px;
            color: #4F46E5;
            margin-bottom: 3px;
        }
        .header .subtitle {
            font-size: 11px;
            color: #666;
        }
        .section {
            margin-bottom: 10px;
            border: 1px solid #ddd;
            padding: 8px;
            border-radius: 3px;
        }
        .section-title {
            font-size: 12px;
            font-weight: bold;
            color: #4F46E5;
            margin-bottom: 6px;
            padding-bottom: 3px;
            border-bottom: 2px solid #4F46E5;
        }
        .info-grid {
            display: table;
            width: 100%;
            margin-top: 5px;
        }
        .info-row {
            display: table-row;
        }
        .info-label {
            display: table-cell;
            font-weight: bold;
            padding: 2px 8px 2px 0;
            width: 30%;
        }
        .info-value {
            display: table-cell;
            padding: 2px 0;
        }
        .sizes-grid {
            display: table;
            width: 100%;
            margin-top: 5px;
            border-collapse: collapse;
        }
        .sizes-row {
            display: table-row;
        }
        .size-cell {
            display: table-cell;
            border: 1px solid #ddd;
            padding: 4px 2px;
            text-align: center;
            width: 10%;
            font-size: 9px;
        }
        .size-cell.header {
            background-color: #4F46E5;
            color: white;
            font-weight: bold;
        }
        .total-box {
            background-color: #F3F4F6;
            padding: 6px;
            margin-top: 5px;
            border-radius: 3px;
            text-align: center;
        }
        .total-box .label {
            font-size: 9px;
            color: #666;
        }
        .total-box .value {
            font-size: 16px;
            font-weight: bold;
            color: #4F46E5;
            margin-top: 2px;
        }
        .footer {
            margin-top: 10px;
            text-align: center;
            font-size: 8px;
            color: #999;
            border-top: 1px solid #ddd;
            padding-top: 5px;
        }
    </style>
</head>
<body>
    <!-- Cabeçalho Principal -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; padding: 15px; background-color: #f8f9fa; border: 2px solid #2c3e50; border-radius: 6px;">
        <div style="font-size: 20px; font-weight: 700; color: #2c3e50; font-family: 'Montserrat', sans-serif;">OS {{ $order->id }}</div>
        <div style="text-align: center;">
            <div style="font-size: 18px; font-weight: 700; color: #2c3e50; font-family: 'Montserrat', sans-serif;">
                @php
                    $firstItem = $order->items->first();
                    $artName = null;
                    if ($firstItem && $firstItem->sublimations) {
                        $firstSublimation = $firstItem->sublimations->first();
                        if ($firstSublimation && $firstSublimation->art_name) {
                            $artName = $firstSublimation->art_name;
                        }
                    }
                @endphp
                {{ $artName ?? 'SEM NOME' }}
            </div>
        </div>
        <div style="font-size: 16px; font-weight: 700; color: #2c3e50; font-family: 'Montserrat', sans-serif;">
            @if($order->delivery_date)
                {{ \Carbon\Carbon::parse($order->delivery_date)->format('d/m/Y') }}
            @else
                SEM DATA
            @endif
        </div>
    </div>

    <!-- Nome do Vendedor -->
    <div style="margin-bottom: 12px; padding: 10px; background-color: #e3f2fd; border: 1px solid #2196f3; border-radius: 6px; text-align: center;">
        <div style="font-size: 14px; font-weight: 600; color: #1976d2; font-family: 'Montserrat', sans-serif;">
            VENDEDOR: {{ $order->seller }}
        </div>
    </div>

    <!-- Destaque para Evento -->
    @if($order->contract_type && strtoupper($order->contract_type) === 'EVENTO')
    <div style="margin-bottom: 15px; padding: 15px; background: linear-gradient(45deg, #ff6b6b, #ff8e8e); border: 3px solid #ff4757; border-radius: 10px; text-align: center; box-shadow: 0 4px 12px rgba(255, 71, 87, 0.4);">
        <div style="font-size: 18px; font-weight: 900; color: #ffffff; font-family: 'Montserrat', sans-serif; text-shadow: 2px 2px 4px rgba(0,0,0,0.5); letter-spacing: 1px;">
            🎉 PEDIDO DE EVENTO 🎉
        </div>
        <div style="font-size: 12px; font-weight: 600; color: #ffffff; margin-top: 4px; text-shadow: 1px 1px 2px rgba(0,0,0,0.3);">
            PRIORIDADE ESPECIAL
        </div>
    </div>
    @endif

    <!-- Itens do Pedido -->
    @foreach($order->items as $item)
    <div style="margin-bottom: 10px; background-color: #ffffff; border: 1px solid #e9ecef; padding: 10px; border-radius: 4px;">
        <div style="background-color: #f8f9fa; color: #495057; padding: 6px 8px; margin: -10px -10px 10px -10px; border-radius: 3px 3px 0 0; font-size: 12px; font-weight: 600; border-bottom: 1px solid #e9ecef;">
            ITEM {{ $item->item_number ?? $loop->iteration }} - {{ $item->quantity }} peças
        </div>

        <!-- Imagem de Capa -->
        @if($item->hasCoverImage && $item->coverImageInfo)
        <div style="text-align: center; background-color: #f8f9fa; margin-bottom: 20px; padding: 15px; border: 1px solid #e9ecef; border-radius: 8px;">
            <div style="font-weight: 600; margin-bottom: 10px; color: #495057; font-size: 14px;">Imagem de Capa</div>
            @if($item->coverImageUrl)
            <img src="file://{{ $item->coverImageUrl }}" alt="Capa" style="max-width: 300px; max-height: 250px; width: auto; height: auto; border-radius: 6px; margin-bottom: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
            @endif
        </div>
        @elseif($item->cover_image)
        <div style="text-align: center; background-color: #fff3cd; margin-bottom: 20px; padding: 15px; border: 1px solid #ffeaa7; border-radius: 8px;">
            <div style="font-weight: 600; margin-bottom: 8px; color: #856404; font-size: 14px;">⚠️ Imagem não encontrada</div>
            <div style="font-size: 11px; color: #856404; line-height: 1.3;">
                Arquivo não encontrado no servidor
            </div>
        </div>
        @endif
        
        <!-- Especificações da Costura -->
        <div style="margin-bottom: 20px;">
            <div style="font-weight: 600; margin-bottom: 12px; font-size: 16px; color: #495057;">Especificações</div>
            <table style="width: 100%; border-collapse: collapse; border: 1px solid #e9ecef; border-radius: 6px; overflow: hidden;">
                <thead>
                    <tr style="background-color: #f8f9fa;">
                        <th style="border: 1px solid #e9ecef; padding: 12px; text-align: center; font-size: 12px; font-weight: 600; color: #495057;">TECIDO</th>
                        <th style="border: 1px solid #e9ecef; padding: 12px; text-align: center; font-size: 12px; font-weight: 600; color: #495057;">COR</th>
                        <th style="border: 1px solid #e9ecef; padding: 12px; text-align: center; font-size: 12px; font-weight: 600; color: #495057;">GOLA</th>
                        <th style="border: 1px solid #e9ecef; padding: 12px; text-align: center; font-size: 12px; font-weight: 600; color: #495057;">MODELO</th>
                        <th style="border: 1px solid #e9ecef; padding: 12px; text-align: center; font-size: 12px; font-weight: 600; color: #495057;">DETALHE</th>
                        <th style="border: 1px solid #e9ecef; padding: 12px; text-align: center; font-size: 12px; font-weight: 600; color: #495057;">ESTAMPA</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="border: 1px solid #e9ecef; padding: 12px; text-align: center; font-size: 12px; font-weight: 500; background-color: #ffffff; color: #212529;">{{ strtoupper($item->fabric ?? 'N/A') }}</td>
                        <td style="border: 1px solid #e9ecef; padding: 12px; text-align: center; font-size: 12px; font-weight: 500; background-color: #ffffff; color: #212529;">{{ strtoupper($item->color ?? 'N/A') }}</td>
                        <td style="border: 1px solid #e9ecef; padding: 12px; text-align: center; font-size: 12px; font-weight: 500; background-color: #ffffff; color: #212529;">{{ strtoupper($item->collar ?? 'N/A') }}</td>
                        <td style="border: 1px solid #e9ecef; padding: 12px; text-align: center; font-size: 12px; font-weight: 500; background-color: #ffffff; color: #212529;">{{ strtoupper($item->model ?? 'N/A') }}</td>
                        <td style="border: 1px solid #e9ecef; padding: 12px; text-align: center; font-size: 12px; font-weight: 500; background-color: #ffffff; color: #212529;">{{ strtoupper($item->detail ?? 'N/A') }}</td>
                        <td style="border: 1px solid #e9ecef; padding: 12px; text-align: center; font-size: 12px; font-weight: 500; background-color: #ffffff; color: #212529;">{{ strtoupper($item->print_type ?? 'N/A') }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Tamanhos -->
        <div>
            <div style="font-weight: 600; margin-bottom: 12px; font-size: 16px; color: #495057;">Tamanhos</div>
            <table style="width: 100%; border-collapse: collapse; border: 1px solid #e9ecef; margin-bottom: 12px; border-radius: 6px; overflow: hidden;">
                <thead>
                    <tr>
                        @foreach(['PP', 'P', 'M', 'G', 'GG', 'EXG', 'G1', 'G2', 'G3', 'ESPECIAL'] as $size)
                        @php
                            $sizeColors = [
                                'PP' => '#FF8C00',      // LARANJA
                                'P' => '#FFD700',       // AMARELO
                                'M' => '#4169E1',       // AZUL
                                'G' => '#DC143C',       // VERMELHO
                                'GG' => '#32CD32',      // VERDE
                                'EXG' => '#8A2BE2',     // ROXO
                                'G1' => '#FFFFFF',      // BRANCO
                                'G2' => '#FFFFFF',      // BRANCO
                                'G3' => '#FFFFFF',      // BRANCO
                                'ESPECIAL' => '#FFFFFF' // BRANCO
                            ];
                            $backgroundColor = $sizeColors[$size] ?? '#f8f9fa';
                        @endphp
                        <th style="border: 1px solid #e9ecef; padding: 12px; text-align: center; font-size: 12px; font-weight: 600; color: #000000; background-color: {{ $backgroundColor }};">{{ $size }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        @foreach(['PP', 'P', 'M', 'G', 'GG', 'EXG', 'G1', 'G2', 'G3', 'ESPECIAL'] as $size)
                        <td style="border: 1px solid #e9ecef; padding: 12px; text-align: center; font-size: 14px; font-weight: 700; background-color: #ffffff; color: #000000;">{{ $item->sizes[$size] ?? 0 }}</td>
                        @endforeach
                    </tr>
                </tbody>
            </table>
            
            <div style="display: flex; justify-content: space-between; align-items: center; background-color: #f8f9fa; color: #2c3e50; padding: 12px; border-radius: 6px; border: 1px solid #e9ecef;">
                <div style="font-size: 14px; font-weight: 600; font-family: 'Montserrat', sans-serif;">TOTAL</div>
                <div style="font-size: 18px; font-weight: 700; font-family: 'Montserrat', sans-serif;">{{ $item->quantity }}</div>
            </div>
            
            @if($item->art_notes)
            <div style="margin-top: 12px; padding: 8px; background-color: #f8d7da; border: 1px solid #dc3545; border-radius: 4px;">
                <div style="font-size: 12px; font-weight: 600; color: #000000; margin-bottom: 4px;">Observações do Item:</div>
                <div style="font-size: 11px; color: #000000; line-height: 1.4;">{{ $item->art_notes }}</div>
            </div>
            @endif
        </div>
    </div>
    @endforeach

    <!-- Observações do Vendedor -->
    @if($order->items->where('sublimations')->flatten()->where('seller_notes')->isNotEmpty())
    <div style="margin-top: 20px;">
        <div style="font-size: 16px; font-weight: 600; color: #495057; margin-bottom: 12px;">Observações do Vendedor</div>
        @foreach($order->items as $item)
            @if($item->sublimations)
                @foreach($item->sublimations as $sub)
                    @if($sub->seller_notes)
                    <div style="margin-bottom: 8px; padding: 8px; background-color: #f8d7da; border: 1px solid #dc3545; border-radius: 4px; font-size: 14px; color: #000000;">
                        <strong style="color: #000000;">{{ $sub->art_name ?? 'Aplicação' }}:</strong> {{ $sub->seller_notes }}
                    </div>
                    @endif
                @endforeach
            @endif
        @endforeach
    </div>
    @endif

    <!-- Observações Gerais -->
    @if($order->notes)
    <div style="margin-top: 20px; border: 1px solid #e9ecef; padding: 15px; border-radius: 8px; background-color: #f8f9fa;">
        <div style="font-size: 14px; font-weight: 600; color: #495057; margin-bottom: 8px;">Observações Gerais</div>
        <div style="padding: 12px; background-color: #ffffff; border-left: 4px solid #ffc107; font-size: 12px; line-height: 1.4; color: #212529;">
            {{ $order->notes }}
        </div>
    </div>
    @endif

    <div class="footer">
        Impresso em {{ date('d/m/Y H:i') }}
    </div>
</body>
</html>
