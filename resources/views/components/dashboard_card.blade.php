<div class="card shadow-sm border-{{ $color }}">
    <div class="card-header text-white bg-{{ $color }}">
        <h6 class="mb-0 d-flex justify-content-between align-items-center">{{ $title }}
            <span class="badge bg-light text-{{ $color }}">{{ $count }}</span>
        </h6>
    </div>
    <div class="card-body text-{{ $color }}">
        <div class="d-flex justify-content-between align-items-center">
            <div class="dash__pie" id="pie__{{ $title }}">
                <div class="dash__percent">
                    <div>
                        <p class="font-weight-bold m-0 text-dark fs-5">
                            {{ $num_solicitudes ? round(($count / $num_solicitudes) * 100) : 0 }}%</p>
                    </div>
                </div>
            </div>
            <p class="fs-1 m-0 text-end"> <strong>{{ $count }}</strong> </p>
        </div>
    </div>
</div>
