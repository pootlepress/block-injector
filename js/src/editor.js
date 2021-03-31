// Add the JS code to this file. On running npm run dev, it will compile to js/dist/.

// export function add( to, howMuch ) {
// 	return to + howMuch;
// }
// document.getElementById("demo").onclick = function()  {alert()};
// export function myFunction(){
//     alert();
// }
jQuery(document).ready(function($){
    $('#pmabDesktopsize').click(function(){
        wp.data.dispatch('core/edit-post').__experimentalSetPreviewDeviceType('Desktop');
    })
    $('#pmabTabletsize').click(function(){
        wp.data.dispatch('core/edit-post').__experimentalSetPreviewDeviceType('Tablet');
    })
    $('#pmabMobilesize').click(function(){
        wp.data.dispatch('core/edit-post').__experimentalSetPreviewDeviceType('Mobile');
    })
    $('#_pmab_meta_type').on('change', function() {
       
        if(this.value == 'post' || this.value == 'page' ){
            $('.specificpost').show();
        }
        else{
            $('.specificpost').hide();
            $('#_pmab_meta_specific_post').val('');
        }
        if(this.value == 'category'){    
            $('.category-box').show();
        }
        else{    
            $('.category-box').hide(); 
            $("#_pmab_meta_category").val('');
        }
        if(this.value == 'tags'){    
            $('.tags').show();
        }
        else{    
            $('.tags').hide(); 
            $('#_pmab_meta_tags').val('');

        }
    });
    $('#_pmab_meta_tag_n_fix').on('change', function() {
       
        if(this.value == 'h2_after' || this.value == 'p_after' ){
            $('.certain_num').show();
        }
        // else if (this.value == 'top_before' || this.value == 'bottom_after' ){
        //     $('.certain_num').hide();
            
        // }
        else{
            $('.certain_num').hide();
            $('#_pmab_meta_number_of_blocks').val('');
        }
    });
});