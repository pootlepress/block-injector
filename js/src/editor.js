jQuery(document).ready(function($){
    $('#_pmab_meta_type').on('change', function() {    
        if(this.value == 'post' || this.value == 'page' ){
            $('.specificpost').show();
        } else{
            $('.specificpost').hide();
            $('#_pmab_meta_specific_post').val('');
        }
        if(this.value == 'category'){    
            $('.category-box').show();
        } else{    
            $('.category-box').hide(); 
            $('#_pmab_meta_category').val('');
        }
        if(this.value == 'tags'){    
            $('.tags').show();
        } else{    
            $('.tags').hide(); 
            $('#_pmab_meta_tags').val('');
        }
    });
    $('#_pmab_meta_tag_n_fix').on('change', function() {  
        if(this.value == 'h2_after' || this.value == 'p_after' ){
            $('.certain_num').show();
        } else{
            $('.certain_num').hide();
            $('#_pmab_meta_number_of_blocks').val('');
        }
    });
    $('#_pmab_meta_type2').on('change', function() {       
        if(this.value == 'post_exclude' || this.value == 'page_exclude' ){
            $('.specificpost_exclude').show();
        } else{
            $('.specificpost_exclude').hide();
            $('#_pmab_meta_specific_post_exclude').val('');
        }        
    });
});