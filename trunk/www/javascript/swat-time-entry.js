function SwatTimeEntry(id)
{
	this.id = id;

	this.hour = document.getElementById(id + '_hour');
	this.minute = document.getElementById(id + '_minute');
	this.second = document.getElementById(id + '_second');
	this.ampm = document.getElementById(id + '_ampm');

	this.swat_date = null;

	var is_ie = (document.addEventListener) ? false : true;
	var self = this;

	function handleChange(event)
	{
		var target;
		if (!event)
			var event = window.event;

		if (event.target)
			target = event.target;
		else if (event.srcElement)
			target = event.srcElement;

		// fix Safari bug
		if (target.nodeType == 3)
			target = target.parentNode;

		self.update(target);
	}

	if (is_ie) {
		if (this.hour)
			this.hour.attachEvent('onchange', handleChange);

		if (this.minute)
			this.minute.attachEvent('onchange', handleChange);

		if (this.second)
			this.second.attachEvent('onchange', handleChange);

		if (this.ampm)
			this.ampm.attachEvent('onchange', handleChange);
	} else {
		if (this.hour)
			this.hour.addEventListener('change', handleChange, true);

		if (this.minute)
			this.minute.addEventListener('change', handleChange, true);

		if (this.second)
			this.second.addEventListener('change', handleChange, true);

		if (this.ampm)
			this.ampm.addEventListener('change', handleChange, true);
	}
}

SwatTimeEntry.prototype.setSwatDate = function(swat_date)
{
	if (typeof SwatDateEntry != 'undefined' &&
		swat_date instanceof SwatDateEntry) {
		this.swat_date = swat_date;
		swat_date.swat_time = this;
	}
}

SwatTimeEntry.prototype.reset = function(reset_date)
{
	if (this.hour)
		this.hour.selectedIndex = 0;

	if (this.minute)
		this.minute.selectedIndex = 0;

	if (this.second)
		this.second.selectedIndex = 0;

	if (this.ampm)
		this.ampm.selectedIndex = 0;

	if (this.swat_date && reset_date)
		this.swat_date.reset(false);
}

SwatTimeEntry.prototype.setNow = function(set_date)
{
	var now = new Date();	
	
	if (now.getHours() < 12) {
		hour_out = now.getHours();
		ampm_out = 1;
	} else {
		hour_out = (now.getHours() - 12);
		ampm_out = 2;
	}
	
	if (this.hour && this.hour.selectedIndex == 0)
		this.hour.selectedIndex = this.getHourIndex(hour_out);
		
	if (this.minute && this.minute.selectedIndex == 0)
		this.minute.selectedIndex = this.getMinuteIndex(now.getMinutes());
		
	if (this.second && this.second.selectedIndex == 0)
		this.second.selectedIndex = this.getSecondIndex(now.getSeconds());
		
	if (this.ampm && this.ampm.selectedIndex == 0)
		this.ampm.selectedIndex = ampm_out;
	
	if (this.swat_date && set_date)
		this.swat_date.setNow(false);
}

SwatTimeEntry.prototype.parseInt = function(serialized_integer)
{
	var value = parseInt(serialized_integer.replace(/[^\d]*/, ''));
	if (isNaN(value))
		return null;

	return value;
}

SwatTimeEntry.prototype.getIntegerIndex = function(flydown, value)
{
	var flydown_value;
	for (i = 0; i < flydown.options.length; i++) {
		flydown_value = this.parseInt(flydown.options[i].value);
		if (flydown_value == value)
			return i;
	}
	return null;
}

SwatTimeEntry.prototype.getHourIndex = function(hour)
{
	return this.getIntegerIndex(this.hour, hour);
}

SwatTimeEntry.prototype.getMinuteIndex = function(minute)
{
	return this.getIntegerIndex(this.minute, minute);
}

SwatTimeEntry.prototype.getSecondIndex = function(second)
{
	return this.getIntegerIndex(this.second, second);
}

SwatTimeEntry.prototype.setDefault = function(set_date)
{
	if (this.hour && this.hour.selectedIndex == 0)
		this.hour.selectedIndex = 1;

	if (this.minute && this.minute.selectedIndex == 0) 
		this.minute.selectedIndex = 1;

	if (this.second && this.second.selectedIndex == 0)
		this.second.selectedIndex = 1;

	if (this.ampm && this.ampm.selectedIndex == 0)
		this.ampm.selectedIndex = 1;

	if (this.swat_date && set_date)
		this.swat_date.setDefault(false);
}

SwatTimeEntry.prototype.update = function(active_flydown)
{
	// hour is required for this, so stop if it doesn't exist
	if (!this.hour) return;
	
	if (this.parseInt(active_flydown.value) != null) {
		var now = new Date();	
		var this_hour = now.getHours();
		
		if (this_hour > 11)
			this_hour = this_hour - 12;
		
		if (this.parseInt(this.hour.value) == this_hour)
			this.setNow(true);
		else
			this.setDefault(true);
	}
}
