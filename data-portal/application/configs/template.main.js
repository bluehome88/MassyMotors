$(document).ready(function(){
	Jax.setOptions({
		remoteConfigUrl: 'Jax/client/application-options',
	});
	Jax.persist("###APPNAMESPACE###.main.js");
	Jax.updateLayout(true);
});