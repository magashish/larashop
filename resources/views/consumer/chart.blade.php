


<div class="package-stats">
    <h3>Package Statistics</h3>
    
    <div class="stat-card">
        <h4>Total Packages Purchased</h4>
        <p class="stat-number">{{ $packageStats['total'] }}</p>
    </div>
    
    <div class="tier-breakdown">
        <h4>Packages by Tier</h4>
        <ul>
            <li>Bronze (ID: 8): <strong>{{ $packageStats['by_tier']['bronze'] }}</strong></li>
            <li>Silver (ID: 20): <strong>{{ $packageStats['by_tier']['silver'] }}</strong></li>
            <li>Gold (ID: 21): <strong>{{ $packageStats['by_tier']['gold'] }}</strong></li>
        </ul>
    </div>
</div>


<div class="package-stats">
    <h3>Package Statistics</h3>
    
    <div class="stat-card total">
        <h4>Total Packages Purchased</h4>
        <p class="stat-number">{{ $packageStats['total'] }}</p>
    </div>
    
    <div class="tier-grid">
        <div class="tier-card bronze">
            <span class="tier-badge">Bronze</span>
            <p class="tier-count">{{ $packageStats['by_tier']['bronze'] }}</p>
            <small>Package ID: 8</small>
        </div>
        
        <div class="tier-card silver">
            <span class="tier-badge">Silver</span>
            <p class="tier-count">{{ $packageStats['by_tier']['silver'] }}</p>
            <small>Package ID: 20</small>
        </div>
        
        <div class="tier-card gold">
            <span class="tier-badge">Gold</span>
            <p class="tier-count">{{ $packageStats['by_tier']['gold'] }}</p>
            <small>Package ID: 21</small>
        </div>
    </div>
</div>

<style>
.tier-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1rem;
    margin-top: 1rem;
}

.tier-card {
    padding: 1.5rem;
    border-radius: 8px;
    text-align: center;
}

.tier-card.bronze { background: #cd7f32; color: white; }
.tier-card.silver { background: #c0c0c0; color: #333; }
.tier-card.gold { background: #ffd700; color: #333; }

.tier-count {
    font-size: 2rem;
    font-weight: bold;
    margin: 0.5rem 0;
}
</style>


<canvas id="dataPathChart"></canvas>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    fetch('/dashboard/data-path-stats')
    .then(response => response.json())
    .then(data => {
        const ctx = document.getElementById('dataPathChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: data.map(d => d.date),
                datasets: [{
                    label: 'Data Path Activity',
                    data: data.map(d => d.count),
                    borderColor: 'rgb(75, 192, 192)',
                    tension: 0.1
                }]
            }
        });
    });
</script>