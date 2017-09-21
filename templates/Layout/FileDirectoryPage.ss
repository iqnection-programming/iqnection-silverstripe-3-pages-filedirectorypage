<h1>$Title</h1>
<p class="breadcrumbs">
	$Breadcrumbs(20,0,0,1)
</p>

<% if $Content %>
	<div class="content">$Content</div>
<% end_if %>

<% if $Children.Count %>
	<div class="child-list sub-dir-list">
		<ul>
			<% loop $Children %>
				<li>
					<a href="$Link">
						<img src="/iq-filedirectorypage/images/dir-icon.png" alt="$Title" />
						<span>$Title</span>
					</a>
				</li>
			<% end_loop %>
		</ul>
	</div>
<% end_if %>

<% if $PagedFiles.Count %>
	<div class="child-list file-list">
		<ul>
			<% loop $PagedFiles %>
				<li>
					<a href="$SecureDownloadLink">
						<div class="file-icon file-{$getExtension}"></div>
						<span>$Title</span>
					</a>
				</li>
			<% end_loop %>
		</ul>
		<% if $PagedFiles.MoreThanOnePage %>
			<p class="pagination">
				<% if $PagedFiles.NotFirstPage %>
					<a class="prev" href="$PagedFiles.PrevLink">Prev</a>
				<% end_if %>
				<% loop $PagedFiles.Pages %>
					<% if $CurrentBool %>
						$PageNum
					<% else %>
						<% if $Link %>
							<a href="$Link">$PageNum</a>
						<% else %>
							...
						<% end_if %>
					<% end_if %>
				<% end_loop %>
				<% if $PagedFiles.NotLastPage %>
					<a class="next" href="$PagedFiles.NextLink">Next</a>
				<% end_if %>
			</p>
		<% end_if %>
	</div>
<% end_if %>