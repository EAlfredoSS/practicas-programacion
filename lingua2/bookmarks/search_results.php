<?php
function generateUserCard($user_id, $user_name, $user_city, $thumb_image, $n_comments, $positive_percentage, $user_country = '')
{
	$user_id = htmlspecialchars($user_id, ENT_QUOTES, 'UTF-8');
	$user_name = htmlspecialchars($user_name, ENT_QUOTES, 'UTF-8');
	$user_city = htmlspecialchars($user_city, ENT_QUOTES, 'UTF-8');
	$user_country = htmlspecialchars($user_country, ENT_QUOTES, 'UTF-8');
	$thumb_image = htmlspecialchars($thumb_image, ENT_QUOTES, 'UTF-8');
	$n_comments = intval($n_comments);
	$positive_percentage = intval($positive_percentage);
	
	ob_start();
	?>
	<div class="col-lg-4 col-md-6 col-sm-6 col-6 mb-4"
         data-user-card="true" 
         data-user-id="<?php echo $user_id; ?>" 
         data-user-name="<?php echo strtolower($user_name); ?>" 
         data-user-city="<?php echo strtolower($user_city); ?>" 
         data-user-country="<?php echo strtolower($user_country); ?>">
		
		<div class="company_profile_info">
			
			<!-- Avatar -->
			<div class="usr-pic">
				<img src="<?php echo $thumb_image; ?>?nocache=<?php echo time(); ?>" alt="<?php echo $user_name; ?>" />
			</div>

			<!-- Información del perfil -->
			<div class="profile_info">
				<h3><?php echo $user_name; ?> <span class="user-id">#<?php echo $user_id; ?></span></h3>
				<p>
					<i class="fas fa-map-marker-alt"></i> 
					<?php echo !empty($user_city) ? $user_city : "Unknown"; ?>
				</p>
			</div>

			<!-- Estadísticas -->
			<div class="user-stats">
				<div class="stat-item">
					<div class="tooltip-container">
						<span class="tooltip-text"><?php echo $n_comments; ?> evaluations received</span>
						<div class="stat-icon star">
							<i class="fas fa-star"></i>
							<span class="stat-number"><?php echo $n_comments; ?></span>
						</div>
					</div>
				</div>
				<div class="stat-item">
					<div class="tooltip-container">
						<span class="tooltip-text"><?php echo $positive_percentage; ?>% positive evaluations</span>
						<div class="stat-icon heart">
							<i class="fas fa-heart"></i>
							<span class="stat-number"><?php echo $positive_percentage; ?>%</span>
						</div>
					</div>
				</div>
			</div>

			<!-- BOTONES -->
			<div class="action-buttons">
				<a href="../user/u.php?identificador=<?php echo $user_id; ?>" class="btn-card btn-view-profile">
					<i class="fas fa-eye"></i>
					<span>View Profile</span>
				</a>
				<a href="javascript:void(0);" 
				   class="btn-card btn-remove-fav" 
				   data-url="./deletebookmark.php?idfav=<?php echo $user_id; ?>"
				   data-username="<?php echo $user_name; ?>">
					<i class="fas fa-times"></i>
					<span>Remove</span>
					<span class="btn-text-long"> from favourites</span>
				</a>
			</div>

			<!-- Notificación -->
			<div class="notification" style="display: none;">Removed successfully!</div>

		</div>
	</div>

	<?php
	return ob_get_clean();
}
?>

<!-- ===== TÍTULO RESULTS ===== -->
<div class="company-title">
    <h3>Results</h3>
</div>

<!-- ===== CONTENEDOR DE TARJETAS ===== -->
<div class="container-fluid">
    <div class="row" id="user-cards-container">
    </div>
</div>

<style type="text/css">

	:root {
		--primary-orange: #d35400;
		--accent-orange:  #e67e22;
		--light-orange:   #fdf2e9;
		--waiting-grey:   #95a5a6;
		--waiting-bg:     #f2f3f4;
		--text-dark:      #2c3e50;
		--text-grey:      #7f8c8d;
		--border-color:   #ecf0f1;
		--border-grey:    #bdc3c7;
		--bg-grey:        #f4f7f6;
	}

	* { box-sizing: border-box; }

	/* ===== TÍTULO ===== */
	.company-title { margin: 20px 0 25px 0; }

	.company-title h3 {
		font-size: 24px;
		font-weight: 600;
		color: var(--text-dark);
		border-left: 5px solid var(--primary-orange);
		padding-left: 15px;
		margin: 0;
	}

	/* ===== TARJETA - BORDE GRIS 1PX + BORDE IZQUIERDO NARANJA ===== */
	.company_profile_info {
		background: white;
		border-radius: 8px;
		box-shadow: 0 3px 10px rgba(0,0,0,0.08);
		border: 1px solid var(--border-grey);
		border-left: 5px solid var(--primary-orange);  /* NARANJA A LA IZQUIERDA */
		padding: 16px;
		margin-bottom: 0;
		height: 100%;
		display: flex;
		flex-direction: column;
		transition: transform 0.2s, box-shadow 0.2s;
	}

	.company_profile_info:hover {
		transform: translateY(-2px);
		box-shadow: 0 5px 15px rgba(0,0,0,0.12);
	}

	/* ===== AVATAR - BORDE NARANJA 2PX Y REDONDO ===== */
	.usr-pic {
		width: 75px;
		height: 75px;
		border-radius: 50%;  /* CÍRCULO PERFECTO */
		border: 2px solid var(--primary-orange);  /* NARANJA DE 2PX */
		box-shadow: 0 2px 8px rgba(0,0,0,0.08);
		overflow: hidden;
		margin: 0 auto 12px;
		flex-shrink: 0;
	}

	.usr-pic img {
		width: 100%;
		height: 100%;
		object-fit: cover;
	}

	/* ===== INFO PERFIL ===== */
	.profile_info {
		text-align: center;
		margin-bottom: 8px;
	}

	.profile_info h3 {
		font-size: 15px;
		font-weight: 700;
		color: var(--text-dark);
		margin: 0 0 3px 0;
		line-height: 1.3;
		word-break: break-word;
	}

	.user-id {
		font-size: 11px;
		font-weight: 400;
		color: #b0b0b0;
	}

	.profile_info p {
		font-size: 12px;
		color: var(--text-grey);
		margin: 0;
		line-height: 1.3;
	}

	.profile_info p i {
		color: var(--accent-orange);
		margin-right: 3px;
		font-size: 11px;
	}

	/* ===== ESTADÍSTICAS ===== */
	.user-stats {
		display: flex;
		justify-content: center;
		gap: 6px;
		margin: 8px 0 10px 0;
		flex-wrap: wrap;
	}

	.stat-item { flex: 0 1 auto; }

	.stat-icon {
		display: flex;
		align-items: center;
		justify-content: center;
		gap: 3px;
		padding: 5px 8px;
		border-radius: 6px;
		font-weight: 600;
		font-size: 11px;
		box-shadow: 0 2px 4px rgba(0,0,0,0.05);
		white-space: nowrap;
	}

	.stat-icon.star  { background: #fff4e5; color: var(--primary-orange); }
	.stat-icon.heart { background: #ffeaea; color: #e74c3c; }
	.stat-number { font-weight: 700; }
	.stat-icon i { font-size: 11px; }

	/* ===== TOOLTIPS ===== */
	.tooltip-container {
		position: relative;
		display: inline-block;
		cursor: help;
	}

	.tooltip-text {
		visibility: hidden;
		width: 130px;
		background-color: var(--text-dark);
		color: white;
		text-align: center;
		border-radius: 6px;
		padding: 6px;
		position: absolute;
		z-index: 10;
		bottom: 130%;
		left: 50%;
		transform: translateX(-50%);
		opacity: 0;
		transition: opacity 0.3s;
		font-size: 11px;
		pointer-events: none;
		box-shadow: 0 4px 12px rgba(0,0,0,0.15);
	}

	.tooltip-text::after {
		content: "";
		position: absolute;
		top: 100%;
		left: 50%;
		margin-left: -5px;
		border-width: 5px;
		border-style: solid;
		border-color: var(--text-dark) transparent transparent transparent;
	}

	.tooltip-container:hover .tooltip-text {
		visibility: visible;
		opacity: 1;
	}

	/* ===== BOTONES ===== */
	.action-buttons {
		display: flex;
		flex-wrap: wrap;
		gap: 6px;
		margin-top: auto;
		padding-top: 12px;
		width: 100%;
	}

	.btn-card {
		flex: 1 1 auto;
		min-width: 0;
		display: inline-flex;
		align-items: center;
		justify-content: center;
		gap: 4px;
		padding: 8px 6px;
		border-radius: 7px;
		font-weight: 600;
		font-size: 10px;
		text-align: center;
		text-decoration: none;
		transition: all 0.2s ease;
		cursor: pointer;
		line-height: 1.3;
		min-height: 34px;
		white-space: normal;
	}

	.btn-card i {
		flex-shrink: 0;
		font-size: 10px;
	}

	.btn-text-long {
		display: inline;
	}

	/* BOTÓN VIEW PROFILE - NARANJA SÓLIDO */
	.btn-view-profile {
		background: var(--primary-orange);  /* NARANJA SÓLIDO */
		color: white;
		border: none;
	}

	.btn-view-profile:hover {
		background: var(--accent-orange);
		color: white;
		transform: translateY(-1px);
		box-shadow: 0 3px 8px rgba(211,84,0,0.2);
		text-decoration: none;
	}

	/* BOTÓN REMOVE - TRANSPARENTE CON BORDE GRIS 1PX */
	.btn-remove-fav {
		background: transparent;
		color: var(--waiting-grey);
		border: 1px solid var(--border-grey);
	}

	.btn-remove-fav i { color: var(--waiting-grey); }

	.btn-remove-fav:hover {
		background: var(--waiting-bg);
		color: #636e72;
		border-color: var(--waiting-grey);
		transform: translateY(-1px);
		text-decoration: none;
	}

	/* ===== NOTIFICACIÓN ===== */
	.notification {
		position: fixed;
		top: 50%;
		left: 50%;
		transform: translate(-50%, -50%);
		background-color: #e77667;
		color: white;
		padding: 15px 25px;
		border-radius: 8px;
		box-shadow: 0 8px 24px rgba(0,0,0,0.2);
		font-size: 16px;
		font-weight: 600;
		z-index: 1000;
		opacity: 0;
		transition: opacity 0.3s ease;
	}

	.notification.show { opacity: 1; }

	/* ===== RESPONSIVE ===== */

	/* Desktop: 3 columnas */
	@media (min-width: 992px) {
		.col-lg-4 { flex: 0 0 33.333333%; max-width: 33.333333%; }
		.btn-text-long { display: none; }
		.company_profile_info { padding: 14px; }
	}

	/* Tablet: 2 columnas */
	@media (min-width: 768px) and (max-width: 991px) {
		.col-md-6 { flex: 0 0 50%; max-width: 50%; }
		.btn-text-long { display: inline; }
		.btn-card { font-size: 11px; }
	}

	/* Móvil grande: 2 columnas */
	@media (min-width: 480px) and (max-width: 767px) {
		.col-sm-6 { flex: 0 0 50%; max-width: 50%; }
		.btn-text-long { display: none; }
		.btn-card { font-size: 10px; padding: 7px 4px; }
		.usr-pic { width: 65px; height: 65px; }
		.profile_info h3 { font-size: 13px; }
	}

	/* Móvil pequeño: 2 columnas optimizadas */
	@media (max-width: 479px) {
		.col-6 { flex: 0 0 50%; max-width: 50%; }
		.company_profile_info { padding: 10px; }
		.usr-pic { width: 55px; height: 55px; }
		.profile_info h3 { font-size: 12px; }
		.profile_info p { font-size: 10px; }
		.stat-icon { font-size: 10px; padding: 4px 6px; }
		.btn-text-long { display: none; }
		.action-buttons { flex-direction: column; }
		.btn-card { width: 100%; font-size: 10px; min-height: 30px; }
		.company-title h3 { font-size: 18px; }
	}
</style>

<script>
	function showNotification(notificationElement) {
		notificationElement.style.display = 'block';
		setTimeout(() => {
			notificationElement.classList.add('show');
			setTimeout(() => {
				notificationElement.classList.remove('show');
				setTimeout(() => {
					notificationElement.style.display = 'none';
					window.location.reload();
				}, 300);
			}, 2000);
		}, 10);
	}

	document.addEventListener('DOMContentLoaded', function () {
		document.querySelectorAll('.btn-remove-fav').forEach(button => {
			button.addEventListener('click', function (e) {
				e.preventDefault();

				const userName = this.getAttribute('data-username');
				const confirmed = confirm(`Are you sure you want to remove ${userName} from favourites?`);
				if (!confirmed) return;

				const url          = this.getAttribute('data-url');
				const card         = this.closest('.company_profile_info');
				const notification = card.querySelector('.notification');
				const originalHTML = this.innerHTML;

				this.style.pointerEvents = 'none';
				this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> <span>Removing...</span>';

				fetch(url, { method: 'GET' })
					.then(response => {
						if (!response.ok) throw new Error('Server error');
						return response.text();
					})
					.then(() => {
						notification ? showNotification(notification)
						             : setTimeout(() => window.location.reload(), 500);
					})
					.catch(error => {
						console.error('Error:', error);
						this.innerHTML = originalHTML;
						this.style.pointerEvents = 'auto';
						alert('Error removing user. Please try again.');
					});
			});
		});
	});
</script>