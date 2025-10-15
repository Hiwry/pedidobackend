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
            font-family: Arial, sans-serif;
            font-size: 9px;
            line-height: 1.2;
            padding: 10px;
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
            border-left: 3px solid #EC4899;
            padding: 6px;
            margin-bottom: 6px;
        }
        .application-header {
            font-weight: bold;
            color: #EC4899;
            margin-bottom: 3px;
            font-size: 10px;
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
    <div class="header">
        <h1>FOLHA DE PERSONALIZAÇÃO</h1>
        <div class="subtitle">Pedido #{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</div>
    </div>


    <!-- Itens do Pedido -->
    @foreach($order->items as $item)
    <div style="page-break-inside: avoid; @if($loop->iteration > 2 && ($loop->iteration - 1) % 2 == 0) page-break-before: always; @endif margin-bottom: 8px; background-color: #FDF2F8; border: 1px solid #ddd; padding: 6px; border-radius: 3px;">
        <div style="background-color: #EC4899; color: white; padding: 4px 6px; margin: -6px -6px 6px -6px; border-radius: 3px 3px 0 0; font-size: 11px; font-weight: bold;">
            ITEM {{ $item->item_number ?? $loop->iteration }} - {{ $item->print_type }}
        </div>

        <div style="padding: 6px;">
            <!-- Nome da Arte -->
            @php
                $artNames = $item->sublimations->pluck('art_name')->filter()->unique();
            @endphp
            @if($artNames->isNotEmpty())
            <div style="margin-bottom: 6px;">
                <div style="font-weight: bold; margin-bottom: 3px; font-size: 10px;">NOME(S) DA ARTE:</div>
                <div style="font-size: 12px; font-weight: bold; padding: 4px; background-color: #F3F4F6; text-align: center;">
                    {{ $artNames->implode(' | ') }}
                </div>
            </div>
            @endif

            <!-- Imagem de Capa -->
            @if($item->hasCoverImage && $item->coverImageInfo)
            <div style="text-align: center; background-color: white; margin-bottom: 6px; padding: 4px; border: 1px solid #EC4899; border-radius: 3px;">
                <div style="font-weight: bold; margin-bottom: 3px; color: #EC4899; font-size: 9px;">📷 IMAGEM DE CAPA</div>
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

            <!-- Aplicações -->
            @if($item->sublimations && $item->sublimations->count() > 0)
            <div style="margin-bottom: 6px;">
                <div style="font-weight: bold; margin-bottom: 4px; font-size: 10px;">APLICAÇÕES:</div>
                
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
                        @if($sub->art_name)
                            🎨 {{ $sub->art_name }}
                        @elseif($sizeName)
                            {{ $sizeName }}@if($sizeDimensions) ({{ $sizeDimensions }})@endif
                        @else
                            {{ $appType }}
                        @endif
                    </div>
                    <div class="application-details">
                        @if($locationName)<strong>Local:</strong> {{ $locationName }} | @endif
                        @if($sizeName)<strong>Tamanho:</strong> {{ $sizeName }}@if($sizeDimensions) ({{ $sizeDimensions }})@endif | @endif
                        <strong>Quantidade:</strong> {{ $sub->quantity }}
                        @if($sub->color_count > 0)
                        | <strong>Cores:</strong> {{ $sub->color_count }}
                        @endif
                        @if($sub->has_neon)
                        | <strong>Neon:</strong> Sim
                        @endif
                    </div>
                    
                    <!-- Imagem da Aplicação -->
                    @if($sub->application_image && extension_loaded('gd'))
                    <div style="margin-top: 4px; text-align: center;">
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
                                 style="max-width: 100px; max-height: 100px; border: 1px solid #EC4899; border-radius: 3px; margin: 2px 0;">
                        @endif
                    </div>
                    @endif
                    
                    <div class="price-row">
                        <span>Preço Unitário: R$ {{ number_format($sub->unit_price, 2, ',', '.') }} × {{ $sub->quantity }}</span>
                        @if($sub->discount_percent > 0)
                        <span style="color: #059669;">Desconto: {{ $sub->discount_percent }}%</span>
                        @endif
                        <span><strong>Subtotal: R$ {{ number_format($sub->final_price, 2, ',', '.') }}</strong></span>
                    </div>
                </div>
                @endforeach

                <!-- Total das Aplicações do Item -->
                <div class="total-box">
                    <div class="total-row">
                        <span>Total de Aplicações:</span>
                        <span>{{ $item->sublimations->count() }}</span>
                    </div>
                    <div class="total-row final">
                        <span>TOTAL ITEM {{ $item->item_number ?? $loop->iteration }}:</span>
                        <span>R$ {{ number_format($item->sublimations->sum('final_price'), 2, ',', '.') }}</span>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
    @endforeach

    <!-- Total Geral de Personalizações -->
    <div style="background-color: #EC4899; color: white; text-align: center; padding: 8px; border-radius: 3px; margin-top: 8px;">
        <div style="font-size: 12px; margin-bottom: 3px;">TOTAL GERAL</div>
        <div style="font-size: 20px; font-weight: bold;">
            R$ {{ number_format($order->items->sum(function($item) { 
                return $item->sublimations->sum('final_price'); 
            }), 2, ',', '.') }}
        </div>
    </div>


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
