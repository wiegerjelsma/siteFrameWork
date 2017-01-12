<script type='text/jhtml' class="template spinner">
	<div class='spinner-wrapper'<jhtml:if test="(typeof id != 'undefined')"> id='${id}-wrapper'</jhtml:if>>
		<div class='spinner'<jhtml:if test="(typeof id != 'undefined')"> id='${id}'</jhtml:if>>&nbsp;</div>
	</div>
</script>

<script type='text/jhtml' class="template popup-title">
	<h4<jhtml:if test="(typeof id != 'undefined')"> id='${id}'</jhtml:if>>${value}</h4>	
</script>

<script type='text/jhtml' class="template popup-content">
	<p<jhtml:if test="(typeof id != 'undefined')"> id='${id}'</jhtml:if>>${value}</p>	
</script>

<script type='text/jhtml' class="template popup-buttoncontainer">
	<hr>
	<div<jhtml:if test="(typeof id != 'undefined')"> id='${id}'</jhtml:if> class='button-container'></div>
</script>

<script type='text/jhtml' class="template popup-buttoncontainer-ul">
	<ul<jhtml:if test="(typeof id != 'undefined')"> id='${id}'</jhtml:if>></ul>
</script>

<script type='text/jhtml' class="template popup-buttoncontainer-button">
	<li class="active"><input<jhtml:if test="(typeof id != 'undefined')"> id='${id}'</jhtml:if> type='button' value='${value}' class='button' /></li>		
</script>

<script type='text/jhtml' class="template popup-buttoncontainer-a">
	<li class=""><a<jhtml:if test="(typeof id != 'undefined')"> id='${id}'</jhtml:if> class='button' href="#">${value}</a></li>
</script>

<script type='text/jhtml' class="template popup-columnwrapper">
	<div class='column-wrapper'<jhtml:if test="(typeof id != 'undefined')"> id='${id}'</jhtml:if>>
	</div>
</script>

<script type='text/jhtml' class="template popup-columnwrapper-image">
	<div class='column-wrapper'<jhtml:if test="(typeof id != 'undefined')"> id='${id}'</jhtml:if>>
		<img <jhtml:if test="(typeof id != 'undefined')"> id='${id}-image'</jhtml:if> />
	</div>
</script>

<script type='text/jhtml' class="template popup-columnwrapper-loading">
	<div class='column-wrapper'<jhtml:if test="(typeof id != 'undefined')"> id='${id}'</jhtml:if>>
	</div>
</script>

<script type='text/jhtml' class="template popup-columnwrapper-form">
	<form <jhtml:if test="(typeof id != 'undefined')"> id='${id}'</jhtml:if>>
	</form>
</script>

<script type='text/jhtml' class="template popup-column">
	<div class='column <jhtml:if test="(typeof size != 'undefined')"> ${size}</jhtml:if>'<jhtml:if test="(typeof id != 'undefined')"> id='${id}'</jhtml:if>></div>
</script>
<script type='text/jhtml' class="template popup-column-content">
		<div class='column-content ${type}'<jhtml:if test="(typeof id != 'undefined')"> id='${id}'</jhtml:if>></div>
</script>

<script type='text/jhtml' class="template popup-column-content-row">
	<div<jhtml:if test="(typeof id != 'undefined')"> id='${id}'</jhtml:if> class='row<jhtml:if test="(typeof oddeven != 'undefined')"> ${oddeven}</jhtml:if>'>	
	<jhtml:if test="(value_id)">
	<input type='${type}' name='${name}' value='<jhtml:if test="(typeof value_id != 'undefined')">${value_id}</jhtml:if>' value_name='${value}' <jhtml:if test="(checked)">checked</jhtml:if> /><p>${ucfirst(value)}</p>
	</jhtml:if>
	</div>
</script>

<script type='text/jhtml' class="template popup-formrow-header">
	<hr>
	<div class='form-header'>
		<h4>${value}</h4> 
	</div>					
</script>

<script type='text/jhtml' class="template popup-formrow">
	<div class='form-row odd ${size}'<jhtml:if test="(typeof id != 'undefined')"> id='${id}'</jhtml:if>>
		<h6>${label}</h6>
	</div>
	<jhtml:if test="(typeof hr != 'undefined')"><hr></jhtml:if>
</script>

<script type='text/jhtml' class="template popup-formrow-input">
	<input<jhtml:if test="(typeof id != 'undefined')"> id='${id}'</jhtml:if> type='text' name='' <jhtml:if test="(typeof placeholder != 'undefined' && placeholder)"> innerlabel='${placeholder}' value='${placeholder}' class='innerlabel'</jhtml:if> />
</script>

<script type='text/jhtml' class="template popup-formrow-inlinebutton">
	<input<jhtml:if test="(typeof id != 'undefined')"> id='${id}'</jhtml:if> type='button' value='${value}' class='button inline' />		
</script>

<script type='text/jhtml' class="template popup">
<div <jhtml:if test="(typeof id != 'undefined')"> id='${id}'</jhtml:if> class='popup content-block form<jhtml:if test="(typeof type != 'undefined')"> ${type}</jhtml:if>'>
	<div class="wrapper"<jhtml:if test="(typeof id != 'undefined')"> id='${id}-wrapper'</jhtml:if>></div>
	<div class='content form'<jhtml:if test="(typeof id != 'undefined')"> id='${id}-content'</jhtml:if>>	
	</div>
	<div class='content-top'>
		<div class='left'></div>
		<div class='center'></div>
		<div class='right'></div>			
	</div>
	<div class='content-center'>
		<div class='left'></div>
		<div class='right'></div>					
	</div>
	<div class='content-bottom'>
		<div class='left'></div>
		<div class='center'></div>
		<div class='right'></div>							
	</div>	
</div>
</script>

<script type='text/jhtml' class="template modal">
<div id='modal'></div>
<div <jhtml:if test="(typeof id != 'undefined')"> id='${id}'</jhtml:if> class='popup content-block form<jhtml:if test="(typeof type != 'undefined')"> ${type}</jhtml:if>'>
	<div class="wrapper"<jhtml:if test="(typeof id != 'undefined')"> id='${id}-wrapper'</jhtml:if>></div>
	<div class='content form'<jhtml:if test="(typeof id != 'undefined')"> id='${id}-content'</jhtml:if>>	
	</div>
	<div class='content-top'>
		<div class='left'></div>
		<div class='center'></div>
		<div class='right'></div>			
	</div>
	<div class='content-center'>
		<div class='left'></div>
		<div class='right'></div>					
	</div>
	<div class='content-bottom'>
		<div class='left'></div>
		<div class='center'></div>
		<div class='right'></div>							
	</div>	
</div>
</script>
