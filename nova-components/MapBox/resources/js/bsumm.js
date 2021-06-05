import $ from 'jquery'
const popper = require( 'popper.js' );
const bootstrap = require( 'bootstrap' );
require( 'datatables.net' );
const DataTable = require( 'datatables.net' );
$(document).ready(function($){
    console.log('bsumm');
    $("body").on("click", ".popup-img-btn", function(e){
        e.preventDefault();

        var imgSrc = $(this).find('.img-src').data('src');
        $("#ImageModal .view-img").attr('src', imgSrc)
        $("#ImageModal").modal()
    })

    $("body").on('click', "#sidebar-container li.leading-tight > a", function(){
        if($(".custom-content-container").length){
            var src = $(this).attr('href');
            document.location.href = src;
        }
    })

    if($("#reviews-table").length){
        var business_id = $("#reviews-table").data('business-id')
        $("#reviews-table").DataTable({
            bFilter: false,
            bLengthChange: false,
            processing: true,
            serverSide: true,
            bSort: true,
            pagingType: "full_numbers",
            dom : "flrtip",
            pageLength: 4,
            fnDrawCallback: function(){
                $("#reviews-table thead").remove()
            } ,
            ajax: {
                url: '/api/v1/reviews-datatable/' + business_id,
                data: function(d){
                },
                complete: function(response){
                }
            }
        })
    }

    if($("#post-images-table").length){
        var business_id = $("#post-images-table").data('business-id')
        $("#post-images-table").DataTable({
            bFilter: false,
            bLengthChange: false,
            processing: true,
            serverSide: true,
            bSort: true,
            pagingType: "full_numbers",
            dom : "flrtip",
            pageLength: 8,
            fnDrawCallback: function(){
                $("#post-images-table thead").remove()
            } ,
            ajax: {
                url: '/api/v1/post-images-datatable/' + business_id,
                data: function(d){
                },
                complete: function(response){
                }
            }
        })
    }
})
