<div class="grid-container" id="grid{{$id}}">	
	<span></span>
	@if ($advancedSearch && $advancedSearchOpened === true)
		<div class="search advanced-search {{isset($searchedValue) && $searchedValue<>'' ? 'searched' : ''}}">
			<form action="{{$url}}" method="get">
				@foreach($fieldsRequest as $field=>$valor)
					@if ($field<>'search')
						<input type="hidden" name="{{$field}}" value="{{$valor}}">
					@endif
				@endforeach
				<fieldset>
					<legend>@lang('Easygrid::grid.Advanced Search')</legend>
					@include('Easygrid::advancedSearch', ['fields'=>$advancedSearchFields])
					<button class="btn-submit-advanced-search btn btn-default" type="submit" title="@lang('Easygrid::grid.Search')">
						<span class="glyphicon glyphicon-search"> </span> @lang('Easygrid::grid.Search')
					</button>
					<a href="{{$urlSimpleSearch}}" class="btn btn-default" title="@lang('Easygrid::grid.Simple Search')"><span class="glyphicon glyphicon-zoom-out"></span></a>
					@if ($totalRows>0)
						<span class="total-info pull-right">
							{{trans_choice('Easygrid::grid.Page :current_page of :total_pages. Total of :total_rows row.', $totalRows, [
								'current_page'=>$currentPage, 
								'total_pages'=>$totalPages, 
								'total_rows'=>$totalRows
							])}}
						</span>
					@endif
				</fieldset>
			</form>
		</div>
	@else
		<div class="search simple-search {{isset($searchedValue) && $searchedValue<>'' ? 'searched' : ''}}">		
			<form action="{{$url}}" method="get">			
				@foreach($fieldsRequest as $field=>$valor)
					@if ($field<>'search')
						<input type="hidden" name="{{$field}}" value="{{$valor}}">
					@endif
				@endforeach
		      	<input type="text" name="search" class="form-control input-search" placeholder="@lang('Easygrid::grid.Search by...')" value="{{$searchedValue}}">
		        <button class="btn-search btn btn-default" type="submit" title="@lang('Easygrid::grid.Search')"><span class="glyphicon glyphicon-search"></span></button>		      	
		      	@if (isset($searchedValue) && $searchedValue<>'')				      		
			       	<button class="btn-clear-search btn btn-default" type="button" title="@lang('Easygrid::grid.Clear search')"><span class="glyphicon glyphicon-remove"></span></button>
		      	@endif
		      	@if ($advancedSearch && $advancedSearchOpened === false)
					<a href="{{$urlAdvancedSearch}}" class="btn-advanced-search btn btn-default" title="@lang('Easygrid::grid.Advanced Search')"><span class="glyphicon glyphicon-zoom-in"></span></a>
		      	@endif
		    </form>
			@if ($totalRows>0)
				<span class="total-info">
					{{trans_choice('Easygrid::grid.Page :current_page of :total_pages. Total of :total_rows row.', $totalRows, [
						'current_page'=>$currentPage, 
						'total_pages'=>$totalPages, 
						'total_rows'=>$totalRows
					])}}
				</span>
			@endif
		</div>
	@endif
	
	<div class="row">
		<div class="col-md-8">
			@if($bulkActions)
			<div class="bulk-action">
				<select name="grid_bulk_action" class="grid_bulk_action" data-token="{{ csrf_token() }}" data-confirm-msg="@lang('Do you really want to apply this action to the selected items?')" data-alert-msg="@lang('Select at least one item to apply the action!')">
					<option value="">@lang('Easygrid::grid.Apply to selected')</option>
					@foreach($bulkActions as $action)
					<option value="{{$action['url']}}">{!!$action['title']!!}</option>
					@endforeach
				</select>		
			</div>
			@endif	
		</div>
		<div class="col-md-4">
			<div class="showing-rows-info pull-right">
				<span>@lang('Easygrid::grid.Showing') </span>
				<select name="rows-per-page" data-url="{{$urlRowsPerPage}}">
					@foreach($rowsPerPage as $nr)
					<option value="{!!$nr!!}" {!!$nr==$currentRowsPerPage ? 'selected' : '' !!}>{!!$nr!!}</option>
					@endforeach
				</select>
				<span>@lang('Easygrid::grid.rows per page.')</span>
			</div>
		</div>
	</div>	
	<div class="table-responsive">
		<table class="table table-bordered table-striped table-hover table-condensed grid">
			<thead>
				<tr>
					@if($checkbox['show'])
						<th>
							<input type="checkbox" class="select-all">
						</th>
					@endif					
					@foreach ($fields as $k=>$v)						
						<th class="{!!$v['alias']!!}">
							<div class="arrows">
								<a href="{{$urlOrder}}&order={!!$v['alias']!!}&direction=asc" title="@lang('Easygrid::grid.Order ascending')" class="arrow-up"></a>
								<a href="{{$urlOrder}}&order={!!$v['alias']!!}&direction=desc" title="@lang('Easygrid::grid.Order descending')" class="arrow-down"></a>
							</div>
							<span>{{$v['label']}}</span>
						</th>					
					@endforeach
					@if (isset($actions))
						<th class="actions">@lang('Easygrid::grid.Actions')</th>
					@endif
				</tr>
			</thead>			
			<tbody>
				@if (isset($rows) && count($rows)>0)
					@foreach ($rows as $row)			
						<tr>
							@if($checkbox['show'])
								<td>
									<input class="grid-checkbox" type="checkbox" name="grid_checkbox_{!!$checkbox['field']!!}" value="{!!$row[$checkbox['field']]!!}"/>
								</td>
							@endif
							@foreach ($fields as $k=>$v)																
								<td class="field {!!$v['alias']!!}">{!!$row[$v['alias']]!!}</td>
							@endforeach
							@if (isset($actions))
								<td class="actions">
									@foreach ($row['gridActions'] as $action)	
										@if($action['method']=='GET')								
											<a href="{!!$action['url']!!}" title="{{$action['title']}}" class="btn btn-xs action btn-default">
												@if (isset($action['icone']))
													<span class="{{$action['icone']}}"></span>
												@endif
												@if ($action['onlyIcon']===false)
													{{$action['title']}}
												@endif
											</a>
										@else
											<form action="{!!$action['url']!!}" method="POST" {!! ($action['confirm']!==false ? 'onsubmit="if(!confirm(\''.addslashes(htmlentities($action['confirm'])).'\')){event.preventDefault; return false;}; "' : '' ); !!} >
												{{csrf_field()}}
												<input type="hidden" name="_method" value="{!!$action['method']!!}">
												<button type="submit" title="{{$action['title']}}" class="btn btn-xs action btn-default">
													@if (isset($action['icone']))
														<span class="{{$action['icone']}}"></span>
													@endif
													@if ($action['onlyIcon']===false)
														{{$action['title']}}
													@endif
												</button>
											</form>
										@endif
									@endforeach
								</td>
							@endif
						</tr>
					@endforeach
				@else
					<tr>
						<td colspan="{!!isset($actions) ? count($fields)+($checkbox['show'] ? 1 : 0)+1 : count($fields)+($checkbox['show'] ? 1 : 0) !!}" class="no-results-found">
							<span>@lang('Easygrid::grid.No results found.')</span>
						</td>
					</tr>
				@endif
			</tbody>
		</table>
	</div>
	@if (isset($rows) && count($rows)>0)
	<div class="row">
		<div class="col-md-{!! $totalPages>1 ? '9' : '12' !!}">	
			@if($allowExport)
			<div class="input-group">				
				<select name="export" class="form-control">
					<option value="">@lang('Easygrid::grid.Select an option to export')</option>
					<option value="xls">XLS</option>
					<option value="csv">CSV</option>
				</select>
				<a href="#" data-href="{{$urlExport}}" target="_blank" class="input-group-addon btn-export" title="@lang('Export')" data-alert-msg="@lang('Easygrid::grid.Select a format for export!')">
					<span class="glyphicon glyphicon-download"></span> @lang('Easygrid::grid.Export')
				</a>
			</div>		
			@endif
		</div>
		<div class="col-md-3">
			@if ($totalPages>1)
				<div class="nav-pagination">
		            <div class="input-group">
						<a href="{!!$urlPreviousPage!!}" class="direction input-group-addon" title="@lang('Easygrid::grid.Previous Page')">
							<span class="glyphicon glyphicon-chevron-left"></span>
						</a>
		                <select class="form-control select-page" data-url="{!!$urlPagination!!}">
		                	@for($i=1; $i<=$totalPages;$i++)
			                    <option value="{!!$i!!}" {{$i==$currentPage ? 'selected' : ''}} >@lang('Easygrid::grid.Page') {!!$i!!}</option>
			                @endfor
		                </select>
						<a href="{!!$urlNextPage!!}" class="direction input-group-addon" title="@lang('Easygrid::grid.Next Page')">
							<span class="glyphicon glyphicon-chevron-right"></span>
						</a>	                
		            </div>
		        </div>
			@endif	
		</div>
	</div>
	@endif
</div>