<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <title>Nota do Pedido #{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            line-height: 1.3;
            color: #000;
            background: white;
        }
        .header {
            text-align: center;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #000;
        }
        .header h1 {
            font-size: 20px;
            color: #000;
            margin-bottom: 3px;
            font-weight: bold;
        }
        .header .subtitle {
            font-size: 12px;
            color: #333;
        }
        .company-info {
            text-align: center;
            margin-bottom: 15px;
            padding: 8px;
            background-color: #f5f5f5;
            border: 1px solid #ccc;
        }
        .company-info h2 {
            font-size: 16px;
            color: #000;
            margin-bottom: 3px;
            font-weight: bold;
        }
        .company-info p {
            font-size: 10px;
            color: #333;
        }
        .section {
            margin-bottom: 12px;
            padding: 8px;
            border: 1px solid #000;
        }
        .section-title {
            font-size: 12px;
            font-weight: bold;
            color: #000;
            margin-bottom: 6px;
            padding-bottom: 2px;
            border-bottom: 1px solid #000;
            text-transform: uppercase;
        }
        .info-grid {
            display: table;
            width: 100%;
        }
        .info-row {
            display: table-row;
        }
        .info-label {
            display: table-cell;
            font-weight: bold;
            padding: 2px 8px 2px 0;
            width: 30%;
            font-size: 10px;
        }
        .info-value {
            display: table-cell;
            padding: 2px 0;
            font-size: 10px;
        }
        .item-section {
            margin-bottom: 12px;
            padding: 8px;
            background-color: #f9f9f9;
            border: 1px solid #ccc;
        }
        .item-header {
            font-size: 11px;
            font-weight: bold;
            color: #000;
            margin-bottom: 6px;
            text-transform: uppercase;
        }
        .application-item {
            background-color: white;
            border-left: 3px solid #000;
            padding: 6px;
            margin-bottom: 6px;
            font-size: 9px;
        }
        .application-header {
            font-weight: bold;
            color: #000;
            margin-bottom: 2px;
        }
        .application-details {
            color: #333;
        }
        .price-row {
            display: flex;
            justify-content: space-between;
            margin-top: 4px;
            padding-top: 4px;
            border-top: 1px dashed #000;
            font-size: 9px;
        }
        .total-section {
            background-color: #000;
            color: white;
            padding: 12px;
            text-align: center;
            margin: 15px 0;
        }
        .total-section h3 {
            font-size: 14px;
            margin-bottom: 3px;
        }
        .total-section .amount {
            font-size: 20px;
            font-weight: bold;
        }
        .payment-info {
            background-color: #f0f0f0;
            border: 1px solid #000;
            padding: 8px;
            margin: 12px 0;
        }
        .payment-info h4 {
            color: #000;
            font-size: 11px;
            margin-bottom: 4px;
            font-weight: bold;
        }
        .payment-details {
            font-size: 10px;
            color: #000;
        }
        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 9px;
            color: #000;
            border-top: 1px solid #000;
            padding-top: 8px;
        }
        .status-badge {
            display: inline-block;
            padding: 2px 6px;
            border: 1px solid #000;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
            background-color: #f0f0f0;
        }
    </style>
</head>
<body>
    <!-- Cabeçalho -->
    <div class="header">
        <h1>NOTA DO PEDIDO</h1>
        <div class="subtitle">Pedido #{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</div>
    </div>

    <!-- Informações da Empresa -->
    <div class="company-info">
        <h2>SUA EMPRESA DE COSTURA</h2>
        <p>Especializada em Personalização e Costura</p>
        <p>Tel: (11) 99999-9999 | Email: contato@empresa.com</p>
        <p>Endereço da Empresa, Cidade - Estado</p>
    </div>

    <!-- Informações do Cliente -->
    <div class="section">
        <div class="section-title">DADOS DO CLIENTE</div>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Nome:</div>
                <div class="info-value">{{ $order->client->name }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Telefone:</div>
                <div class="info-value">{{ $order->client->phone_primary }}</div>
            </div>
            @if($order->client->email)
            <div class="info-row">
                <div class="info-label">Email:</div>
                <div class="info-value">{{ $order->client->email }}</div>
            </div>
            @endif
            @if($order->client->cpf_cnpj)
            <div class="info-row">
                <div class="info-label">CPF/CNPJ:</div>
                <div class="info-value">{{ $order->client->cpf_cnpj }}</div>
            </div>
            @endif
        </div>
    </div>

    <!-- Status e Datas -->
    <div class="section">
        <div class="section-title">STATUS DO PEDIDO</div>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Status Atual:</div>
                <div class="info-value">
                    <span class="status-badge status-{{ strtolower(str_replace(' ', '-', $order->status->name)) }}" 
                          style="background-color: {{ $order->status->color }}20; color: {{ $order->status->color }}">
                        {{ $order->status->name }}
                    </span>
                </div>
            </div>
            <div class="info-row">
                <div class="info-label">Data do Pedido:</div>
                <div class="info-value">{{ $order->created_at->format('d/m/Y H:i') }}</div>
            </div>
            @if($order->delivery_date)
            <div class="info-row">
                <div class="info-label">Data de Entrega:</div>
                <div class="info-value">{{ \Carbon\Carbon::parse($order->delivery_date)->format('d/m/Y') }}</div>
            </div>
            @endif
        </div>
    </div>

    <!-- Itens do Pedido -->
    @foreach($order->items as $item)
    <div class="item-section">
        <div class="item-header">ITEM {{ $loop->iteration }} - {{ $item->print_type }}</div>
        
        <!-- Nome da Arte -->
        @if($item->art_name)
        <div style="margin-bottom: 8px;">
            <div style="font-weight: bold; margin-bottom: 3px; font-size: 11px;">NOME DA ARTE:</div>
            <div style="font-size: 12px; font-weight: bold; padding: 5px; background-color: white; text-align: center; border: 1px solid #E5E7EB;">
                {{ $item->art_name }}
            </div>
        </div>
        @endif


        <!-- Detalhes da Costura -->
        <div style="margin-bottom: 8px;">
            <div style="font-weight: bold; margin-bottom: 3px; font-size: 11px;">ESPECIFICAÇÕES DA COSTURA:</div>
            <div class="info-grid">
                <div class="info-row">
                    <div class="info-label">Tecido:</div>
                    <div class="info-value">{{ $item->fabric }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Cor:</div>
                    <div class="info-value">{{ $item->color }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Tipo de Impressão:</div>
                    <div class="info-value">{{ $item->print_type }}</div>
                </div>
            </div>
        </div>

        <!-- Aplicações -->
        @if($item->sublimations && $item->sublimations->count() > 0)
        <div style="margin-bottom: 8px;">
            <div style="font-weight: bold; margin-bottom: 3px; font-size: 11px;">APLICAÇÕES DE PERSONALIZAÇÃO:</div>
            
            @foreach($item->sublimations as $index => $sub)
            @php
                $sizeName = $sub->size ? $sub->size->name : $sub->size_name;
                $sizeDimensions = $sub->size ? $sub->size->dimensions : '';
                $locationName = $sub->location ? $sub->location->name : $sub->location_name;
                $appType = $sub->application_type ? strtoupper($sub->application_type) : 'APLICAÇÃO';
            @endphp
            <div class="application-item">
                <div class="application-header">
                    Aplicação {{ $index + 1 }}: 
                    @if($sizeName)
                        {{ $sizeName }}@if($sizeDimensions) ({{ $sizeDimensions }})@endif
                    @else
                        {{ $appType }}
                    @endif
                </div>
                <div class="application-details">
                    @if($locationName)<strong>Local:</strong> {{ $locationName }} | @endif
                    <strong>Quantidade:</strong> {{ $sub->quantity }}
                    @if($sub->color_count > 0)
                    | <strong>Cores:</strong> {{ $sub->color_count }}
                    @endif
                    @if($sub->has_neon)
                    | <strong>Neon:</strong> Sim
                    @endif
                </div>
                <div class="price-row">
                    <span>Preço Unitário: R$ {{ number_format($sub->unit_price, 2, ',', '.') }} × {{ $sub->quantity }}</span>
                    @if($sub->discount_percent > 0)
                    <span style="color: #059669;">Desconto: {{ $sub->discount_percent }}%</span>
                    @endif
                    <span><strong>Subtotal: R$ {{ number_format($sub->final_price, 2, ',', '.') }}</strong></span>
                </div>
            </div>
            @endforeach
        </div>
        @endif

        <!-- Tamanhos -->
        <div style="margin-bottom: 8px;">
            <div style="font-weight: bold; margin-bottom: 3px; font-size: 10px;">TAMANHOS:</div>
            <div style="display: table; width: 100%; border-collapse: collapse; font-size: 9px;">
                <div style="display: table-row; background-color: #000; color: white;">
                    @foreach(['PP', 'P', 'M', 'G', 'GG', 'EXG', 'G1', 'G2', 'G3', 'ESPECIAL'] as $size)
                    <div style="display: table-cell; border: 1px solid #000; padding: 2px; text-align: center; font-weight: bold;">{{ $size }}</div>
                    @endforeach
                </div>
                <div style="display: table-row;">
                    @foreach(['PP', 'P', 'M', 'G', 'GG', 'EXG', 'G1', 'G2', 'G3', 'ESPECIAL'] as $size)
                    <div style="display: table-cell; border: 1px solid #000; padding: 2px; text-align: center;">{{ $item->sizes[$size] ?? 0 }}</div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    @endforeach

    <!-- Resumo Financeiro -->
    <div class="total-section">
        <h3>TOTAL DO PEDIDO</h3>
        <div class="amount">R$ {{ number_format($order->total, 2, ',', '.') }}</div>
    </div>

    <!-- Informações de Pagamento -->
    @if($payment)
    <div class="payment-info">
        <h4>INFORMAÇÕES DE PAGAMENTO</h4>
        <div class="payment-details">
            <div style="margin-bottom: 5px;">
                <strong>Total Pago:</strong> R$ {{ number_format($payment->entry_amount, 2, ',', '.') }}
            </div>
            <div style="margin-bottom: 5px;">
                <strong>Restante:</strong> R$ {{ number_format($payment->remaining_amount, 2, ',', '.') }}
            </div>
            <div>
                <strong>Método:</strong> {{ ucfirst($payment->method) }}
            </div>
            @if($payment->notes)
            <div style="margin-top: 5px;">
                <strong>Observações:</strong> {{ $payment->notes }}
            </div>
            @endif
        </div>
    </div>
    @endif

    <!-- Observações -->
    @if($order->notes)
    <div class="section">
        <div class="section-title">OBSERVAÇÕES</div>
        <div style="padding: 6px; background-color: #f0f0f0; border-left: 3px solid #000; font-size: 10px;">
            {{ $order->notes }}
        </div>
    </div>
    @endif

    <!-- Rodapé -->
    <div class="footer">
        <p><strong>Obrigado pela preferência!</strong></p>
        <p>Esta nota serve como comprovante do seu pedido.</p>
        <p>Para dúvidas, entre em contato conosco.</p>
        <p>Impresso em {{ date('d/m/Y H:i') }}</p>
    </div>
</body>
</html>
