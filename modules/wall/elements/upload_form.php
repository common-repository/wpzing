<?php

/*
 * WPzing: Integrate Zingsphere's services into your WordPress blog
 * Copyright (c) 2012 Zingsphere Ltd.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
*/

?>

<div id="upload_form_title">
	<a href="javascript:void(0);" onClick="App.toggleUploadForm();">Upload your RSS feed file</a>
</div>
<div id="upload_form" style="display: none; border: 1px solid #CCCCCC; margin-top: 5px;">
	<p>
		&nbsp;&nbsp;To import your existing subscriptions, you must have your subscriptions in a standard format called <a href="http://en.wikipedia.org/wiki/OPML" target="_blank">OPML</a>.</p>
	<div>
		<p><input type="file" name="rss" value="Upload" style="margin: 5px;" /> <span id="zWait" style="display: none">Please wait while we parse the file and get all articles &nbsp;<img src="<?php echo admin_url()?>images/loading.gif" /></span></p>
	</div>
	<input type="button" value="Upload"  style="margin: 0 5px 5px 5px;" onClick="App.uploadOPML()"/>
	<input type="submit" name="zWallUploadRss" id="zWallUploadRss" style="display: none"/>
</div>
