	function checkReload() {
	    var dd = new Date();
	    var hh = dd.getHours();
	    var mm = dd.getMinutes();
	    var ss = dd.getSeconds();

			var $http, $self = arguments.callee;

			if (window.XMLHttpRequest) {
				$http = new XMLHttpRequest();
			} else if (window.ActiveXObject) {
				try {
					$http = new ActiveXObject('Msxml2.XMLHTTP');
				} catch(e) {
					$http = new ActiveXObject('Microsoft.XMLHTTP');
				}
			}

			if ($http) {
				$http.onreadystatechange = function() {
						if (/4|^complete$/.test($http.readyState)) {
							  if ($http.responseText == 0) {
										 setTimeout(function(){$self();}, 1000); 
								} else if ($http.responseText == 1) {
									   window.location.reload();
								}
						} 
				}
			}
			$http.open('GET', 'live.php' + '?' + new Date().getTime(), true);
			$http.send(null);

	}

	function ChangeHeight() {
		 var height = $('#ReloadThis')[0].scrollHeight;
		 $('#ReloadThis').scrollTop(height);
	}

		

			function ReloadDiv()
			{
				var
					$http,
					$self = arguments.callee;

				if (window.XMLHttpRequest) {
					$http = new XMLHttpRequest();
				} else if (window.ActiveXObject) {
					try {
						$http = new ActiveXObject('Msxml2.XMLHTTP');
					} catch(e) {
						$http = new ActiveXObject('Microsoft.XMLHTTP');
					}
				}

				if ($http) {
					$http.onreadystatechange = function()
					{
						if (/4|^complete$/.test($http.readyState)) {
							document.getElementById('ReloadThis').innerHTML = $http.responseText;
							$("#ReloadThis").scrollTop($("#ReloadThis")[0].scrollHeight);
							setTimeout(function(){$self();}, 10000);
						}
					};
					$http.open('GET', 'get_google_comments.php' + '?' + new Date().getTime(), true);
					$http.send(null);
				}
			}


