<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <title>Pedido #{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }} - Personalização</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Montserrat', sans-serif;
            font-size: 10px;
            line-height: 1.2;
            padding: 8px;
            margin: 0;
            width: 100%;
            background-color: #ffffff;
        }
        .header {
            text-align: center;
            margin-bottom: 10px;
            border-bottom: 2px solid #EC4899;
            padding-bottom: 8px;
        }
        .header h1 {
            font-size: 16px;
            color: #EC4899;
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
            color: #EC4899;
            margin-bottom: 6px;
            padding-bottom: 3px;
            border-bottom: 2px solid #EC4899;
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
        .cover-image {
            text-align: center;
            margin: 6px 0;
        }
        .cover-image img {
            max-width: 100%;
            max-height: 150px;
            border: 1px solid #EC4899;
            border-radius: 3px;
        }
        .application-item {
            background-color: #F9FAFB;
            border-left: 2px solid #2c3e50;
            padding: 4px;
            margin-bottom: 4px;
        }
        .application-header {
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 2px;
            font-size: 9px;
        }
        .application-details {
            font-size: 8px;
            color: #666;
        }
        .price-row {
            display: flex;
            justify-content: space-between;
            margin-top: 3px;
            padding-top: 3px;
            border-top: 1px dashed #ddd;
            font-size: 8px;
        }
        .total-box {
            background-color: #FEF3C7;
            padding: 6px;
            margin-top: 6px;
            border-radius: 3px;
            border: 1px solid #F59E0B;
        }
        .total-row {
            display: flex;
            justify-content: space-between;
            padding: 2px 0;
            font-size: 9px;
        }
        .total-row.final {
            font-size: 12px;
            font-weight: bold;
            color: #059669;
            border-top: 1px solid #F59E0B;
            padding-top: 4px;
            margin-top: 3px;
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
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px; padding: 8px; background-color: #f8f9fa; border: 1px solid #2c3e50; border-radius: 4px;">
        <div style="font-size: 14px; font-weight: 700; color: #2c3e50; font-family: 'Montserrat', sans-serif;">OS {{ $order->id }}</div>
        <div style="text-align: center;">
            <div style="font-size: 12px; font-weight: 700; color: #2c3e50; font-family: 'Montserrat', sans-serif;">
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
        <div style="font-size: 10px; font-weight: 700; color: #2c3e50; font-family: 'Montserrat', sans-serif;">
            @if($order->delivery_date)
                {{ \Carbon\Carbon::parse($order->delivery_date)->format('d/m/Y') }}
            @else
                SEM DATA
            @endif
        </div>
    </div>

    <!-- Nome do Vendedor -->
    <div style="margin-bottom: 8px; padding: 6px; background-color: #e3f2fd; border: 1px solid #2196f3; border-radius: 4px; text-align: center;">
        <div style="font-size: 11px; font-weight: 600; color: #1976d2; font-family: 'Montserrat', sans-serif;">
            VENDEDOR: {{ $order->seller }}
        </div>
    </div>

    <!-- Destaque para Evento -->
    @if($order->contract_type && strtoupper($order->contract_type) === 'EVENTO')
    <div style="margin-bottom: 10px; padding: 12px; background: linear-gradient(45deg, #ff6b6b, #ff8e8e); border: 3px solid #ff4757; border-radius: 8px; text-align: center; box-shadow: 0 4px 12px rgba(255, 71, 87, 0.4);">
        <div style="font-size: 16px; font-weight: 900; color: #ffffff; font-family: 'Montserrat', sans-serif; text-shadow: 2px 2px 4px rgba(0,0,0,0.5); letter-spacing: 1px;">
            🎉 PEDIDO DE EVENTO 🎉
        </div>
        <div style="font-size: 10px; font-weight: 600; color: #ffffff; margin-top: 3px; text-shadow: 1px 1px 2px rgba(0,0,0,0.3);">
            PRIORIDADE ESPECIAL
        </div>
    </div>
    @endif


    <!-- Itens do Pedido -->
    @foreach($order->items as $item)
    <div style="margin-bottom: 6px; background-color: #ffffff; border: 1px solid #e9ecef; padding: 4px; border-radius: 3px;">
        <div style="background-color: #f8f9fa; color: #2c3e50; padding: 3px 4px; margin: -4px -4px 4px -4px; border-radius: 3px 3px 0 0; font-size: 9px; font-weight: bold; border-bottom: 1px solid #e9ecef;">
            ITEM {{ $item->item_number ?? $loop->iteration }} - {{ $item->print_type }}
        </div>

        <div style="padding: 4px;">
            <!-- Nome da Arte -->
            @php
                $artNames = $item->sublimations->pluck('art_name')->filter()->unique();
            @endphp
            @if($artNames->isNotEmpty())
            <div style="margin-bottom: 4px;">
                <div style="font-weight: bold; margin-bottom: 2px; font-size: 8px;">NOME(S) DA ARTE:</div>
                <div style="font-size: 10px; font-weight: bold; padding: 3px; background-color: #F3F4F6; text-align: center;">
                    {{ $artNames->implode(' | ') }}
                </div>
            </div>
            @endif

            <!-- Imagem de Capa -->
            @if($item->hasCoverImage && $item->coverImageInfo)
            <div style="text-align: center; background-color: white; margin-bottom: 4px; padding: 3px; border: 1px solid #e9ecef; border-radius: 3px;">
                <div style="font-weight: bold; margin-bottom: 2px; color: #2c3e50; font-size: 8px;">IMAGEM DE CAPA</div>
                @if($item->coverImageUrl)
                <img src="file://{{ $item->coverImageUrl }}" alt="Capa" style="max-width: 280px; max-height: 180px; border-radius: 6px; margin-bottom: 6px; box-shadow: 0 2px 6px rgba(0,0,0,0.15);">
                @endif
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

            <!-- Aplicações -->
            @if($item->sublimations && $item->sublimations->count() > 0)
            <div style="margin-bottom: 6px;">
                <div style="font-weight: bold; margin-bottom: 3px; font-size: 9px; color: #2c3e50;">APLICAÇÕES:</div>
                
                @foreach($item->sublimations as $index => $sub)
                @php
                    $sizeName = $sub->size ? $sub->size->name : $sub->size_name;
                    $sizeDimensions = $sub->size ? $sub->size->dimensions : '';
                    $locationName = $sub->location ? $sub->location->name : $sub->location_name;
                    $appType = $sub->application_type ? strtoupper($sub->application_type) : 'APLICAÇÃO';
                @endphp
                <div style="display: table; width: 100%; margin-bottom: 8px; background-color: #F9FAFB; border-left: 2px solid #2c3e50; padding: 8px;">
                    <div style="display: table-row;">
                        <!-- Imagem da Aplicação -->
                        @if($sub->application_image && extension_loaded('gd'))
                        <div style="display: table-cell; width: 50%; vertical-align: top; text-align: center; padding-right: 12px;">
                            @php
                                $appImagePath = storage_path('app/public/' . $sub->application_image);
                                $appImageData = '';
                                
                                if (file_exists($appImagePath)) {
                                    try {
                                        $imageInfo = @getimagesize($appImagePath);
                                        if ($imageInfo) {
                                            $maxWidth = 200;
                                            $maxHeight = 200;
                                            
                                            // Carregar imagem original
                                            $sourceImage = null;
                                            if ($imageInfo['mime'] == 'image/jpeg') {
                                                $sourceImage = @imagecreatefromjpeg($appImagePath);
                                            } elseif ($imageInfo['mime'] == 'image/png') {
                                                $sourceImage = @imagecreatefrompng($appImagePath);
                                            } elseif ($imageInfo['mime'] == 'image/gif') {
                                                $sourceImage = @imagecreatefromgif($appImagePath);
                                            }
                                            
                                            if ($sourceImage) {
                                                $width = imagesx($sourceImage);
                                                $height = imagesy($sourceImage);
                                                
                                                // Calcular proporções
                                                $ratio = min($maxWidth / $width, $maxHeight / $height);
                                                $newWidth = (int)($width * $ratio);
                                                $newHeight = (int)($height * $ratio);
                                                
                                                // Criar imagem redimensionada
                                                $newImage = imagecreatetruecolor($newWidth, $newHeight);
                                                imagecopyresampled($newImage, $sourceImage, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
                                                
                                                // Converter para base64
                                                ob_start();
                                                imagejpeg($newImage, null, 85);
                                                $imageContent = ob_get_clean();
                                                
                                                $appImageData = 'data:image/jpeg;base64,' . base64_encode($imageContent);
                                                
                                                imagedestroy($sourceImage);
                                                imagedestroy($newImage);
                                            }
                                        }
                                    } catch (\Exception $e) {
                                        // Silenciar erro
                                    }
                                }
                            @endphp
                            @if($appImageData)
                                <img src="{{ $appImageData }}" alt="Aplicação {{ $index + 1 }}" 
                                     style="max-width: 200px; max-height: 200px; border: 1px solid #e9ecef; border-radius: 6px; box-shadow: 0 2px 6px rgba(0,0,0,0.15);">
                            @endif
                        </div>
                        @endif
                        
                        <!-- Tabela de Informações da Aplicação -->
                        <div style="display: table-cell; width: 50%; vertical-align: top;">
                            <div style="background-color: #7b1fa2; color: white; padding: 4px 8px; margin-bottom: 6px; border-radius: 3px; font-size: 9px; font-weight: bold; text-align: center;">
                                APLICAÇÃO {{ $index + 1 }}
                            </div>
                            
                            <table style="width: 100%; border-collapse: collapse; font-size: 8px;">
                                <thead>
                                    <tr style="background-color: #f8f9fa;">
                                        <th style="border: 1px solid #dee2e6; padding: 4px; text-align: left; font-weight: bold; color: #495057;">LOCAL</th>
                                        <th style="border: 1px solid #dee2e6; padding: 4px; text-align: left; font-weight: bold; color: #495057;">TAMANHO</th>
                                        <th style="border: 1px solid #dee2e6; padding: 4px; text-align: left; font-weight: bold; color: #495057;">QTD</th>
                                        <th style="border: 1px solid #dee2e6; padding: 4px; text-align: left; font-weight: bold; color: #495057;">CORES</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td style="border: 1px solid #dee2e6; padding: 4px; background-color: #e3f2fd; color: #1976d2; font-weight: 600;">
                                            {{ $locationName ?? '-' }}
                                        </td>
                                        <td style="border: 1px solid #dee2e6; padding: 4px; background-color: #f3e5f5; color: #7b1fa2; font-weight: 600;">
                                            {{ $sizeName ?? '-' }}@if($sizeDimensions) ({{ $sizeDimensions }})@endif
                                        </td>
                                        <td style="border: 1px solid #dee2e6; padding: 4px; background-color: #e8f5e8; color: #388e3c; font-weight: 600;">
                                            {{ $sub->quantity }}
                                        </td>
                                        <td style="border: 1px solid #dee2e6; padding: 4px; background-color: #fff3e0; color: #f57c00; font-weight: 600;">
                                            @if($sub->color_count > 0)
                                                {{ $sub->color_count }} cor{{ $sub->color_count > 1 ? 'es' : '' }}
                                                @if($sub->has_neon)
                                                    + Neon
                                                @endif
                                            @else
                                                -
                                            @endif
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            
                            @if($sub->art_name)
                            <div style="margin-top: 6px; padding: 4px; background-color: #f8f9fa; border: 1px solid #dee2e6; border-radius: 3px; font-size: 8px;">
                                <strong style="color: #495057;">Arte:</strong> {{ $sub->art_name }}
                            </div>
                            @endif
                            
                            @if($sub->color_details)
                            <div style="margin-top: 6px; padding: 4px; background-color: #fff3e0; border: 1px solid #ffb74d; border-radius: 3px; font-size: 8px;">
                                <strong style="color: #f57c00;">Cores:</strong> {{ $sub->color_details }}
                            </div>
                            @endif
                            
                            @if($sub->seller_notes)
                            <div style="margin-top: 6px; padding: 6px; background-color: #f8d7da; border: 1px solid #dc3545; border-radius: 3px; font-size: 12px; color: #000000;">
                                <strong style="color: #000000;">Obs Vendedor:</strong> {{ $sub->seller_notes }}
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach

                <!-- Total das Aplicações do Item -->
                <div class="total-box">
                    <div class="total-row">
                        <span>Total de Aplicações:</span>
                        <span>{{ $item->sublimations->count() }}</span>
                    </div>
                </div>
            </div>
            @endif

            <!-- Tamanhos -->
            <div style="margin-bottom: 12px;">
                <div style="font-weight: 600; margin-bottom: 6px; font-size: 12px; color: #2c3e50; font-family: 'Montserrat', sans-serif;">Tamanhos</div>
                <table style="width: 100%; border-collapse: collapse; border: 1px solid #e9ecef; margin-bottom: 6px; border-radius: 4px; overflow: hidden;">
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
                            <th style="border: 1px solid #e9ecef; padding: 6px; text-align: center; font-size: 9px; font-weight: 600; color: #000000; background-color: {{ $backgroundColor }}; font-family: 'Montserrat', sans-serif;">{{ $size }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            @foreach(['PP', 'P', 'M', 'G', 'GG', 'EXG', 'G1', 'G2', 'G3', 'ESPECIAL'] as $size)
                            <td style="border: 1px solid #e9ecef; padding: 6px; text-align: center; font-size: 10px; font-weight: 700; background-color: #ffffff; color: #000000; font-family: 'Montserrat', sans-serif;">{{ $item->sizes[$size] ?? 0 }}</td>
                            @endforeach
                        </tr>
                    </tbody>
                </table>
                
                <div style="display: flex; justify-content: space-between; align-items: center; background-color: #f8f9fa; color: #2c3e50; padding: 6px; border-radius: 4px; border: 1px solid #e9ecef;">
                    <div style="font-size: 10px; font-weight: 600; font-family: 'Montserrat', sans-serif;">TOTAL</div>
                    <div style="font-size: 12px; font-weight: 700; font-family: 'Montserrat', sans-serif;">{{ $item->quantity }}</div>
                </div>
            </div>
        </div>
    </div>
    @endforeach



    <!-- Observações -->
    @if($order->notes)
    <div style="margin-top: 8px; border: 1px solid #ddd; padding: 6px; border-radius: 3px;">
        <div style="font-size: 11px; font-weight: bold; color: #EC4899; margin-bottom: 4px;">OBSERVAÇÕES</div>
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
