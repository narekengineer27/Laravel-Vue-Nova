@extends('nova::layout')

@section('content')
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.css">
    <link rel="stylesheet" type="text/css" href="/css/custom.css">

    <div id="business-summary" class="custom-content-container">
        <div class="row">
            <div class="col-sm">
                <h2>
                    {{$business->name}}
                </h2>
                <br>
                <div class="mb-2">
                    <div><strong>Categories:</strong></div>
                    @foreach($business->categories as $business_category)
                        <div>
                            <span>{{$business_category->name}}</span>
                            <span>({{$business_category->pivot->relevance}}%)</span>
                        </div>
                    @endforeach
                </div>
                <div class="mb-2">
                    <strong>Score:</strong>
                    <span>{{$business->score}}%</span>
                </div>
                @if (count($business->contacts))
			<div class="mb-2">
			    <strong><u>Contacts</u></strong><br>
			    <table width="100%">
				@foreach($business->contacts as $contact)
				    <tr>
					<td width="150px">{{$contact->type}}</td>
					<td>{{$contact->value}}</td>
				    </tr>
				@endforeach
			    </table>
			</div>
                @endif
            </div>
            <div class="col-sm business-map">
                <map-box-detail businessid="{{$business->id}}" lat="{{$business->lat}}" lng="{{$business->lng}}"></map-box-detail>
            </div>
        </div>
        <div class="row">
            <div class="col-sm mb-2">
                <strong>Bio:</strong>
                @if ($business->bio)
                    <p>{{$business->bio}}</p>
                @else
                    <span class="content-none">None</span>
                @endif
            </div>
        </div>

        <div class="mb-2">
            <strong>Attributes:</strong>
            @if (count($business->optionalAttributes))
                <div class="row">
                    <div class="col-sm-6 m-2">
                        <table class="table table-striped table-bordered attribute-table">
                            <tr>
                                <th>Attribut Name</th>
                                <th>Attribute Description</th>
                            </tr>
                            @foreach($business->optionalAttributes as $optionalAttribute)
                                <tr>
                                    <td>{{$optionalAttribute->name}}</td>
                                    <td>{{$optionalAttribute->pivot->description}}</td>
                                </tr>
                            @endforeach
                        </table>
                    </div>
                </div>
            @else
                <span class="content-none">None</span>
            @endif
        </div>

        <div class="mb-2">
			<div class="row">
				<div class="col-sm-3 m-2">
					<strong>Top Keywords:</strong>
					@if (count($business->topKeywords()))
						<table class="table table-striped table-bordered attribute-table">
							<tr>
								<th>Keyword</th>
								<th>Count</th>
							</tr>
							@foreach($business->topKeywords() as $keyword)
								<tr>
									<td>{{$keyword->keyword}}</td>
									<td>{{$keyword->cnt}}</td>
								</tr>
							@endforeach
						</table>
					@else
						<span class="content-none"><br>None</span>
					@endif
				</div>

				<div class="col-sm-4 m-2">
					<strong>Top Topics:</strong>
					@php
						$topics = $business->getTopics();
					@endphp
					@if (count($topics))
						<table class="table table-striped table-bordered attribute-table">
							<tr>
								<th>Topic</th>
								<th>Count</th>
								<th>Rating</th>
							</tr>
							@foreach($topics as $topic)
								<tr>
									<td>{{$topic['title']}}</td>
									<td>{{$topic['total']}}</td>
									<td>{{$topic['score']}}%</td>
								</tr>
							@endforeach
						</table>
					@else
						<span class="content-none"><br>None</span>
					@endif
				</div>
			</div>
        </div>

		@if (count($topics))
		<strong>Topic Details</strong>
        <div class="mb-2" style="max-height: 400px; overflow-y: scroll; overflow-x: hidden;">
			<div class="row">
				@foreach($topics as $topic)
					<div class="col-sm-4">
						<strong>{{$topic['title']}}:</strong>
						<table class="table table-striped table-bordered attribute-table">
							<tr>
								<th>Phrase</th>
								<th>Count</th>
							</tr>
							@foreach($topic['phrases'] as $pr)
								<tr>
									<td>{{$pr['keyword']}}</td>
									<td>{{$pr['cnt']}}</td>
								</tr>
							@endforeach
						</table>
					</div>
				@endforeach
			</div>
        </div> <!-- /story-details -->
		@endif

        <div class="mb-2">
            <strong>Post Images:</strong>
            @if (count($postImages))
                <table id="post-images-table" class="table table-bordered table-striped table-condensed dataTable" data-business-id="{{$business->id}}">
                    <thead >
                        <tr>
                            <th>&nbsp;</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>

            @else
                <span class="content-none">None</span>
            @endif
        </div>

        <div class="mb-3">
            <strong>Reviews:</strong>
            @if (count($reviews))
                <table id="reviews-table" class="table table-bordered table-striped table-condensed dataTable" data-business-id="{{$business->id}}">
                    <thead >
                        <tr>
                            <th>&nbsp;</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>

            @else
                <span class="content-none">None</span>
            @endif
        </div>

        <div id="ImageModal" class="modal fade " tabindex="-1" role="dialog">
          <div class="modal-dialog modal-full">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                  </div>
                <div class="modal-body text-center">
                    <img src="//placehold.it/1000x600" class="view-img ">
                </div>
            </div>
          </div>
        </div>
        <loading ref="loading"></loading>
    </div>
    {{--<script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>--}}
    {{--<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>--}}
    {{--<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>--}}
    {{--<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.js"></script>--}}

    {{--<script src="{{asset('js/summary-custom.js')}}" ></script>--}}
@endsection
