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
            font-family: Arial, sans-serif;
            font-size: 9px;
            line-height: 1.2;
            padding: 10px;
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
    <div class="header">
        <h1>FOLHA DE COSTURA</h1>
        <div class="subtitle">Pedido #{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</div>
    </div>

    <!-- Informações do Cliente -->
    <div style="margin-bottom: 8px; border: 1px solid #ddd; padding: 6px; border-radius: 3px;">
        <div style="font-size: 11px; font-weight: bold; color: #4F46E5; margin-bottom: 4px; padding-bottom: 2px; border-bottom: 2px solid #4F46E5;">CLIENTE</div>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Nome:</div>
                <div class="info-value">{{ $order->client->name }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Tel:</div>
                <div class="info-value">{{ $order->client->phone_primary ?? '-' }}</div>
            </div>
            @if($order->delivery_date)
            <div class="info-row">
                <div class="info-label">Entrega:</div>
                <div class="info-value">{{ \Carbon\Carbon::parse($order->delivery_date)->format('d/m/Y') }}</div>
            </div>
            @endif
        </div>
    </div>

    <!-- Itens do Pedido -->
    @foreach($order->items as $item)
    <div style="page-break-inside: avoid; @if($loop->iteration > 2 && ($loop->iteration - 1) % 2 == 0) page-break-before: always; @endif margin-bottom: 8px; background-color: #F9FAFB; border: 1px solid #ddd; padding: 6px; border-radius: 3px;">
        <div style="background-color: #4F46E5; color: white; padding: 4px 6px; margin: -6px -6px 6px -6px; border-radius: 3px 3px 0 0; font-size: 11px; font-weight: bold;">
            ITEM {{ $item->item_number ?? $loop->iteration }} - {{ $item->quantity }} peças
        </div>

        <!-- Imagem de Capa -->
        @if($item->hasCoverImage && $item->coverImageInfo)
        <div style="text-align: center; background-color: white; margin-bottom: 6px; padding: 4px; border: 1px solid #4F46E5; border-radius: 3px;">
            <div style="font-weight: bold; margin-bottom: 3px; color: #4F46E5; font-size: 9px;">📷 IMAGEM DE CAPA</div>
            @if($item->coverImageUrl)
            <img src="file://{{ $item->coverImageUrl }}" alt="Capa" style="max-width: 220px; max-height: 150px; border-radius: 3px; margin-bottom: 3px;">
            @endif
            <div style="font-size: 7px; color: #666; line-height: 1.3;">
                <strong>{{ $item->coverImageInfo['name'] }}</strong> | 
                <strong>{{ $item->coverImageInfo['extension'] }}</strong> | 
                <strong>{{ $item->coverImageInfo['size'] }}</strong>
            </div>
        </div>
        @elseif($item->cover_image)
        <div style="text-align: center; background-color: #FEF3C7; margin-bottom: 6px; padding: 8px; border: 1px solid #F59E0B; border-radius: 3px;">
            <div style="font-weight: bold; margin-bottom: 3px; color: #F59E0B; font-size: 10px;">⚠️ IMAGEM DE CAPA NÃO ENCONTRADA</div>
            <div style="font-size: 8px; color: #92400E; line-height: 1.4;">
                <strong>Arquivo não encontrado no servidor.</strong><br>
                <strong>Caminho da imagem:</strong> {{ $item->cover_image }}
            </div>
        </div>
        @endif
        
        <!-- Especificações da Costura -->
        <div style="margin-bottom: 6px;">
            <div style="font-weight: bold; margin-bottom: 3px; font-size: 10px;">ESPECIFICAÇÕES:</div>
            <div class="info-grid">
                <div class="info-row">
                    <div class="info-label">Tecido:</div>
                    <div class="info-value">{{ $item->fabric }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Cor do Tecido:</div>
                    <div class="info-value">{{ $item->color }}</div>
                </div>
                @if($item->collar)
                <div class="info-row">
                    <div class="info-label">Gola:</div>
                    <div class="info-value">{{ $item->collar }}</div>
                </div>
                @endif
                @if($item->detail)
                <div class="info-row">
                    <div class="info-label">Detalhe:</div>
                    <div class="info-value">{{ $item->detail }}</div>
                </div>
                @endif
                @if($item->model)
                <div class="info-row">
                    <div class="info-label">Tipo de Corte:</div>
                    <div class="info-value">{{ $item->model }}</div>
                </div>
                @endif
                <div class="info-row">
                    <div class="info-label">Personalização:</div>
                    <div class="info-value">{{ $item->print_type }}</div>
                </div>
            </div>
        </div>

        <!-- Tamanhos -->
        <div>
            <div style="font-weight: bold; margin-bottom: 4px; font-size: 10px;">TAMANHOS:</div>
            <div class="sizes-grid">
                <div class="sizes-row">
                    @foreach(['PP', 'P', 'M', 'G', 'GG', 'EXG', 'G1', 'G2', 'G3', 'ESPECIAL'] as $size)
                    <div class="size-cell header">{{ $size }}</div>
                    @endforeach
                </div>
                <div class="sizes-row">
                    @foreach(['PP', 'P', 'M', 'G', 'GG', 'EXG', 'G1', 'G2', 'G3', 'ESPECIAL'] as $size)
                    <div class="size-cell">{{ $item->sizes[$size] ?? 0 }}</div>
                    @endforeach
                </div>
            </div>
            
            <div class="total-box">
                <div class="label">TOTAL</div>
                <div class="value">{{ $item->quantity }}</div>
            </div>
        </div>
    </div>
    @endforeach

    <!-- Total Geral -->
    <div style="background-color: #10B981; color: white; text-align: center; padding: 8px; border-radius: 3px; margin-top: 8px;">
        <div style="font-size: 12px; margin-bottom: 3px;">TOTAL GERAL</div>
        <div style="font-size: 20px; font-weight: bold;">{{ $order->items->sum('quantity') }} PEÇAS</div>
    </div>

    <!-- Observações -->
    @if($order->notes)
    <div style="margin-top: 8px; border: 1px solid #ddd; padding: 6px; border-radius: 3px;">
        <div style="font-size: 11px; font-weight: bold; color: #4F46E5; margin-bottom: 4px;">OBSERVAÇÕES</div>
        <div style="padding: 4px; background-color: #FEF3C7; border-left: 3px solid #F59E0B; font-size: 9px;">
            {{ $order->notes }}
        </div>
    </div>
    @endif

    <div class="footer">
        Impresso em {{ date('d/m/Y H:i') }}
    </div>
</body>
</html>
