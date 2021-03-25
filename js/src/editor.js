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
    });
});