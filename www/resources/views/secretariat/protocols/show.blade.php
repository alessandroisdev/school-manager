<x-app-layout>
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="h3 mb-1 text-dark fw-bold">Protocolo {{ $protocol->protocol_number }}</h2>
                <p class="text-muted small mb-0">Visualização e Controle de Prazos</p>
            </div>
            <a href="{{ route('secretariat.protocols.index') }}" class="btn btn-outline-secondary fw-bold shadow-sm">
                <i class="bi bi-arrow-left me-1"></i> Voltar
            </a>
        </div>

        <div class="row g-4">
            <!-- Detalhes do Protocolo -->
            <div class="col-md-8">
                <div class="glass-card p-4 h-100">
                    <h5 class="fw-bold text-dark border-bottom pb-2 mb-4">Informações do Documento</h5>
                    
                    <div class="row mb-3">
                        <div class="col-md-4"><span class="text-muted small fw-bold">Remetente:</span></div>
                        <div class="col-md-8 fw-bold">{{ $protocol->sender }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4"><span class="text-muted small fw-bold">Assunto:</span></div>
                        <div class="col-md-8">{{ $protocol->subject }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4"><span class="text-muted small fw-bold">Recebido em:</span></div>
                        <div class="col-md-8">{{ $protocol->received_date->format('d/m/Y') }} (Por: {{ $protocol->receiver ? $protocol->receiver->name : 'Sistema' }})</div>
                    </div>
                    <div class="row mb-4">
                        <div class="col-md-4"><span class="text-muted small fw-bold">Descrição / Histórico:</span></div>
                        <div class="col-md-8">
                            <div class="bg-light p-3 rounded" style="white-space: pre-wrap;">{{ $protocol->description ?? 'Sem descrições adicionais.' }}</div>
                        </div>
                    </div>

                    <h5 class="fw-bold text-dark border-bottom pb-2 mb-3 mt-4">Anexos Digitais ({{ $protocol->attachments->count() }})</h5>
                    
                    <div class="row g-3">
                        @forelse($protocol->attachments as $attachment)
                            <div class="col-md-6">
                                <div class="border rounded p-3 d-flex align-items-center">
                                    <div class="fs-1 me-3 text-danger">
                                        @if(str_contains($attachment->file_type, 'pdf'))
                                            <i class="bi bi-file-earmark-pdf-fill"></i>
                                        @elseif(str_contains($attachment->file_type, 'image'))
                                            <i class="bi bi-file-earmark-image-fill text-primary"></i>
                                        @else
                                            <i class="bi bi-file-earmark-text-fill text-secondary"></i>
                                        @endif
                                    </div>
                                    <div>
                                        <div class="fw-bold text-truncate" style="max-width: 150px;">{{ $attachment->file_name }}</div>
                                        <div class="small text-muted">{{ number_format($attachment->file_size / 1024, 2) }} KB</div>
                                        <a href="{{ Storage::url($attachment->file_path) }}" target="_blank" class="btn btn-sm btn-link p-0 mt-1">Baixar Arquivo</a>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12 text-muted text-center py-3">Nenhum arquivo anexado fisicamente a este protocolo.</div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Controle de Status e Prazos -->
            <div class="col-md-4">
                <div class="glass-card p-4">
                    <h5 class="fw-bold text-dark border-bottom pb-2 mb-4">Controle e SLA</h5>
                    
                    <div class="mb-4">
                        <span class="text-muted small fw-bold d-block mb-1">Prazo de Resposta (SLA):</span>
                        @if($protocol->due_date)
                            @php
                                $daysLeft = now()->diffInDays($protocol->due_date, false);
                                $color = 'success';
                                if($daysLeft < 0) $color = 'danger';
                                elseif($daysLeft <= 3) $color = 'warning';
                            @endphp
                            <div class="fs-4 fw-bold text-{{ $color }}">
                                {{ $protocol->due_date->format('d/m/Y') }}
                                <span class="badge bg-{{ $color }} fs-6 ms-2">
                                    {{ $daysLeft < 0 ? 'Atrasado' : ($daysLeft == 0 ? 'Vence Hoje' : $daysLeft . ' dias restantes') }}
                                </span>
                            </div>
                        @else
                            <span class="fs-5 text-muted">Documento sem prazo legal.</span>
                        @endif
                    </div>

                    <form action="{{ route('secretariat.protocols.update-status', $protocol) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <div class="mb-3">
                            <label class="form-label fw-bold text-muted small">Status Atual</label>
                            <select name="status" class="form-select border-2">
                                <option value="pending" {{ $protocol->status === 'pending' ? 'selected' : '' }}>Pendente (Aguardando Ação)</option>
                                <option value="in_progress" {{ $protocol->status === 'in_progress' ? 'selected' : '' }}>Em Análise (Processando)</option>
                                <option value="resolved" {{ $protocol->status === 'resolved' ? 'selected' : '' }}>Resolvido / Respondido</option>
                                <option value="archived" {{ $protocol->status === 'archived' ? 'selected' : '' }}>Arquivado (Sem Resposta)</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold text-muted small">Adicionar Nota ao Histórico (Opcional)</label>
                            <textarea name="note" class="form-control" rows="2" placeholder="Ex: Encaminhado para a diretoria avaliar."></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 fw-bold shadow-sm">
                            Atualizar Status
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
