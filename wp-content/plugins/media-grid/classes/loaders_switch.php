<?php
// print loader's CSS code 

function mg_loaders_switch() {
	$color = get_option('mg_loader_color', '#888888');
	$lb_color = get_option('mg_item_icons_color', '#333333');
	
	
	switch(get_option('mg_loader', 'default')) {
		
		/* media grid loader */
		case 'default':
		default:
			?>
            #mg_lb_loader .mg_loader {
                transform: 			scale(0.9) translateZ(0);
                -webkit-transform: 	scale(0.9) translateZ(0);
            }
            .mgl_1, .mgl_2, .mgl_3, .mgl_4 {
                background-color: #777;
                width: 11px;
                height: 11px;
                position: absolute;
                top: 0;
                left: 0;
                border-radius: 1px;
                
                -webkit-animation: mg_loader 2s infinite ease-in-out;
                animation: mg_loader 2s infinite ease-in-out;
            }
            .mg_loader div {
                background-color: <?php echo $color ?>;
            }
            #mg_lb_wrap .mg_loader div {
                background-color: <?php echo $lb_color ?>;
            }
            .mgl_2 {
                -webkit-animation-delay: -0.5s;
                animation-delay: -0.5s;
            }
            .mgl_3 {
                -webkit-animation-delay: -1s;
                animation-delay: -1s;
            }
            .mgl_4 {
                -webkit-animation-delay: -1.5s;
                animation-delay: -1.5s;
            }
            @-webkit-keyframes mg_loader {
                25% { -webkit-transform: translate3d(15px, 0, 0) rotate(-90deg) scale(0.3); }
                50% { -webkit-transform: translate3d(15px, 15px, 0) rotate(-180deg); }
                75% { -webkit-transform: translate3d(0, 15px, 0) rotate(-270deg) scale(0.3); }
                100% { -webkit-transform: rotate(-360deg); }
            }
            @keyframes mg_loader {
                25% { transform:  translate3d(15px, 0, 0) rotate(-90deg) scale(0.3); } 
                50% { transform: translate3d(15px, 15px, 0) rotate(-179deg); } 
                75% { transform: translate3d(0, 15px, 0) rotate(-270deg) scale(0.3); } 
                100% { transform: rotate(-360deg); }
            }
            <?php
			break;
		
			
			
		/* rotating square */
		case 'rotating_square':
			?>
			.mg_loader {
                background-color: <?php echo $color ?>;
              
                -webkit-animation: mg-rotateplane 1.2s infinite ease-in-out;
                animation: mg-rotateplane 1.2s infinite ease-in-out;
            }
            #mg_lb_wrap .mg_loader {
                background-color: <?php echo $lb_color ?>;
            }
            .mg_grid_wrap .mg_loader {
                width: 32px;
                height: 32px;	
                margin-top: -16px;
                margin-left: -16px;
            }
            @-webkit-keyframes mg-rotateplane {
                0% 	{-webkit-transform: perspective(120px);}
                50% 	{-webkit-transform: perspective(120px) rotateY(180deg);}
                100% 	{-webkit-transform: perspective(120px) rotateY(180deg)  rotateX(180deg);}
            }
            @keyframes mg-rotateplane {
                0%	{transform: perspective(120px) rotateX(0deg) rotateY(0deg);} 
                50%	{transform: perspective(120px) rotateX(-180.1deg) rotateY(0deg);} 
                100%	{transform: perspective(120px) rotateX(-180deg) rotateY(-179.9deg);}
            }
			<?php
			break;
			
			
			
		/* overlapping circles */
		case 'overlapping_circles':
			?>
            .mg_loader div {
                background-color: <?php echo $color ?>;
            }
            #mg_lb_wrap .mg_loader div {
                background-color: <?php echo $lb_color ?>;
            }
			.mg_loader {
                width: 32px;
                height: 32px;	
                margin-top: -16px;
                margin-left: -16px;
            }
            .mgl_1, .mgl_2 {
                width: 100%;
                height: 100%;
                border-radius: 50%;
                opacity: 0.6;
                position: absolute;
                top: 0;
                left: 0;
                
                -webkit-animation: mg-bounce 1.8s infinite ease-in-out;
                animation: mg-bounce 1.8s infinite ease-in-out;
            }
            .mgl_2 {
                -webkit-animation-delay: -1.0s;
                animation-delay: -1.0s;
            }
            
            @-webkit-keyframes mg-bounce {
                0%, 100% {-webkit-transform: scale(0.0);}
                50% {-webkit-transform: scale(1.0);}
            }
            @keyframes mg-bounce {
                0%, 100% {transform: scale(0.0);} 
                50% {transform: scale(1.0);}
            }
			<?php
			break;
			
			
			
		/* stretching rectangles */
		case 'stretch_rect':
			?>
			.mg_loader div {
                background-color: <?php echo $color ?>;
            }
            #mg_lb_wrap .mg_loader div {
                background-color: <?php echo $lb_color ?>;
            }
            .mgl_1, .mgl_2, .mgl_3 {
                height: 100%;
                width: 6px;
                display: inline-block;
                position: absolute;
                
                -webkit-animation: mg-stretchdelay 1.1s infinite ease-in-out;
                animation: mg-stretchdelay 1.1s infinite ease-in-out;
            }
            .mgl_2 {
                left: 10px;
                -webkit-animation-delay: -1s;
                animation-delay: -1s;
            }
            .mgl_3 {
                right: 0;
                -webkit-animation-delay: -.9s;
                animation-delay: -.9s;
            }
            @-webkit-keyframes mg-stretchdelay {
                0%, 40%, 100% {-webkit-transform: scaleY(0.6);}  
                20% {-webkit-transform: scaleY(1.1);}
            }
            @keyframes mg-stretchdelay {
                0%, 40%, 100% {transform: scaleY(0.6);}  
                20% {transform: scaleY(1.1);}
            }
			<?php
			break;
			
			
			
		/* spin and fill square */
		case 'spin_n_fill_square':
			?>
            .mg_loader {
                border-color: <?php echo $color ?>;
            }
            #mg_lb_wrap .mg_loader {
            	border-color: <?php echo $lb_color ?>;
            }
            .mg_loader div {
                background-color: <?php echo $color ?>;
            }
            #mg_lb_loader .mg_loader div,
            #mg_lb_contents .mg_loader div {
                background-color: <?php echo $lb_color ?>;
            }
            
            .mg_loader {
                border-size: 3px;
                border-style: solid;
                
                -webkit-animation: mg_spinNfill 2.3s infinite ease;
                animation: mg_spinNfill 2.3s infinite ease;
            }
            #mg_lb_loader .mg_loader {
                -moz-box-sizing: border-box;
                box-sizing: border-box;	
            }
            .mgl_1 {
                vertical-align: top;
                width: 100%;
                
                -webkit-animation: mg_spinNfill-inner 2.3s infinite ease-in;
                animation: mg_spinNfill-inner 2.3s infinite ease-in;
            }
            
            @-webkit-keyframes mg_spinNfill {
                0% {-webkit-transform: rotate(0deg);}
                25%, 50% {-webkit-transform: rotate(180deg);}
                75%, 100% {-webkit-transform: rotate(360deg);}
            }
            @keyframes mg_spinNfill {
                0% {transform: rotate(0deg);}
                25%, 50%  {transform: rotate(180deg);}
                75%, 100% {transform: rotate(360deg);}
            }
            @-webkit-keyframes mg_spinNfill-inner {
                0%, 25%, 100% {height: 0%;}
                50%, 75% {height: 100%;}
            }
            @keyframes mg_spinNfill-inner {
                0%, 25%, 100% {height: 0%;}
                50%, 75% {height: 100%;}
            }
			<?php
			break;
			
			
			
		/* pulsing circle */
		case 'pulsing_circle':
			?>
            .mg_loader {
                border-radius: 100%;  
                background-color: <?php echo $color ?>;
                
                -webkit-animation: mg-scaleout 1.0s infinite ease-in-out;
                animation: mg-scaleout 1.0s infinite ease-in-out;
            }
            #mg_lb_wrap .mg_loader {
           		background-color: <?php echo $lb_color ?>;
            }
            .mg_grid_wrap .mg_loader {
                width: 36px;
                height: 36px;	
                margin-top: -18px;
                margin-left: -18px;
            }
            @-webkit-keyframes mg-scaleout {
                0% { -webkit-transform: scale(0);}
                100% {
                  -webkit-transform: scale(1.0);
                  opacity: 0;
                }
            }
            @keyframes mg-scaleout {
                0% {transform: scale(0);} 
                100% {
                  transform: scale(1.0);
                  opacity: 0;
                }
            }
			<?php
			break;	
			
			
			
		/* spinning dots */
		case 'spinning_dots':
			?>
            .mg_loader div {
                background-color: <?php echo $color ?>;
            }
            #mg_lb_wrap .mg_loader div {
                background-color: <?php echo $lb_color ?>;
            }
            .mg_loader {
              text-align: center;
              -webkit-animation: mg-rotate 1.6s infinite linear;
              animation: mg-rotate 1.6s infinite linear;
            }
            .mg_grid_wrap .mg_loader {
                width: 36px;
                height: 36px;	
                margin-top: -18px;
                margin-left: -18px;
            }
            .mgl_1, .mgl_2 {
                width: 57%;
                height: 57%;
                display: inline-block;
                position: absolute;
                top: 0;
                border-radius: 100%;
                
                -webkit-animation: mg-bounce 1.6s infinite ease-in-out;
                animation: mg-bounce 1.6s infinite ease-in-out;
            }
            .mgl_2 {
                top: auto;
                bottom: 0;
                -webkit-animation-delay: -.8s;
                animation-delay: -.8s;
            }
            @-webkit-keyframes mg-rotate {
                0% { -webkit-transform: rotate(0deg) }
                100% { -webkit-transform: rotate(360deg) }
            }
            @keyframes mg-rotate { 
                0% { transform: rotate(0deg); -webkit-transform: rotate(0deg) }
                100% { transform: rotate(360deg); -webkit-transform: rotate(360deg) }
            }
            @-webkit-keyframes mg-bounce {
                0%, 100% {-webkit-transform: scale(0);}
                50% {-webkit-transform: scale(1);}
            }
            @keyframes mg-bounce {
                0%, 100% {transform: scale(0.0);} 
                50% {transform: scale(1.0);}
            }
			<?php
			break;	
			
			
			
		/* appearing cubes */
		case 'appearing_cubes':
			?>
            .mg_loader div {
                background-color: <?php echo $color ?>;
            }
            #mg_lb_wrap .mg_loader div {
                background-color: <?php echo $lb_color ?>;
            }
            .mgl_1, .mgl_2, .mgl_3, .mgl_4 {
                width: 50%;
                height: 50%;
                float: left;
                
                -webkit-animation:	mg-cubeGridScaleDelay 1.3s infinite ease-in-out;
                animation: 			mg-cubeGridScaleDelay 1.3s infinite ease-in-out; 
            }
            .mg_grid_wrap .mg_loader {
                width: 36px;
                height: 36px;	
                margin-top: -18px;
                margin-left: -18px;
            }
            .mgl_1, .mgl_4 {
              	-webkit-animation-delay: .1s;
                      animation-delay: .1s; 
            }
            .mgl_2 {
              	-webkit-animation-delay: .2s;
                		animation-delay: .2s; 
            }
            @-webkit-keyframes mg-cubeGridScaleDelay {
                0%, 70%, 100% {
                  -webkit-transform: scale3D(1, 1, 1);
                          transform: scale3D(1, 1, 1);
                } 35% {
                  -webkit-transform: scale3D(0, 0, 1);
                          transform: scale3D(0, 0, 1); 
                }
            }
            @keyframes mg-cubeGridScaleDelay {
                0%, 70%, 100% {
                  -webkit-transform: scale3D(1, 1, 1);
                          transform: scale3D(1, 1, 1);
                } 35% {
                  -webkit-transform: scale3D(0, 0, 1);
                          transform: scale3D(0, 0, 1);
                } 
            }
			<?php
			break;
			
			
			
		/* folding cube */
		case 'folding_cube':
			?>
            .mg_loader div:before {
                background-color: <?php echo $color ?>;
            }
            #mg_lb_wrap .mg_loader div:before {
                background-color: <?php echo $lb_color ?>;
            }
            .mg_loader {
              -webkit-transform: rotateZ(45deg);
                      transform: rotateZ(45deg);
            }
            #mg_lb_loader .mg_loader {
              -webkit-transform: scale(0.9) rotateZ(45deg);
                      transform: scale(0.9) rotateZ(45deg);
            }
            .mgl_1, .mgl_2, .mgl_3, .mgl_4 {
              float: left;
              width: 50%;
              height: 50%;
              position: relative;
              -webkit-transform: scale(1.1);
                  -ms-transform: scale(1.1);
                      transform: scale(1.1); 
            }
            .mg_loader div:before {
              content: '';
              position: absolute;
              top: 0;
              left: 0;
              width: 100%;
              height: 100%;
              -webkit-animation: mg-foldCubeAngle 2.3s infinite linear both;
                      animation: mg-foldCubeAngle 2.3s infinite linear both;
                      
              -webkit-transform-origin: 100% 100%;
                  -ms-transform-origin: 100% 100%;
                      transform-origin: 100% 100%;
            }
            .mgl_2 {
              -webkit-transform: scale(1.1) rotateZ(90deg);
                      transform: scale(1.1) rotateZ(90deg);
            }
            .mgl_3 {
              -webkit-transform: scale(1.1) rotateZ(270deg);
                      transform: scale(1.1) rotateZ(270deg);
            }
            .mgl_4 {
              -webkit-transform: scale(1.1) rotateZ(180deg);
                      transform: scale(1.1) rotateZ(180deg);
            }
            .mg_loader .mgl_2:before {
              -webkit-animation-delay: 0.3s;
                      animation-delay: 0.3s;
            }
            .mg_loader .mgl_3:before {
              -webkit-animation-delay: 0.9s;
                      animation-delay: 0.9s;
            }
            .mg_loader .mgl_4:before {
              -webkit-animation-delay: 0.6s;
                      animation-delay: 0.6s; 
            }
            @-webkit-keyframes mg-foldCubeAngle {
              0%, 10% {
              	-webkit-transform: perspective(140px) rotateX(-180deg);
                opacity: 0; 
              } 
              25%, 75% {
                -webkit-transform: perspective(140px) rotateX(0deg);
                opacity: 1; 
              } 
              90%, 100% {
                -webkit-transform: perspective(140px) rotateY(180deg);
                opacity: 0; 
              } 
            }
            @keyframes mg-foldCubeAngle {
              0%, 10% {
                transform: perspective(140px) rotateX(-180deg);
                opacity: 0; 
              } 
              25%, 75% {
                transform: perspective(140px) rotateX(0deg);
                opacity: 1; 
              } 
              90%, 100% {
                transform: perspective(140px) rotateY(180deg);
                opacity: 0; 
              }
            }
			<?php
			break;
			
			
			
		/* old-style circles spinner */
		case 'old_style_spinner':
			?>
            .mg_loader div:before {
                color: <?php echo $color ?>;
            }
            #mg_lb_wrap .mg_loader div:before {
                color: <?php echo $lb_color ?>;
            }
			.mg_loader {            	
                font-size: 20px;
                border-radius: 50%;
  
                -webkit-animation: mg-circles-spinner 1.3s infinite linear;
                animation: mg-circles-spinner 1.3s infinite linear;
                
                -webkit-transform: 	scale(0.28) translateZ(0);
                transform: 			scale(0.28) translateZ(0);	
            }
            #mg_lb_loader .mg_loader {
                -webkit-transform: 	scale(0.18) translateZ(0);
                transform: 			scale(0.18) translateZ(0);	
            }
            @-webkit-keyframes mg-circles-spinner {
              0%,
              100%	{box-shadow: 0 -3em 0 0.2em, 2em -2em 0 0em, 3em 0 0 -1em, 2em 2em 0 -1em, 0 3em 0 -1em, -2em 2em 0 -1em, -3em 0 0 -1em, -2em -2em 0 0;}
              12.5% {box-shadow: 0 -3em 0 0, 2em -2em 0 0.2em, 3em 0 0 0, 2em 2em 0 -1em, 0 3em 0 -1em, -2em 2em 0 -1em, -3em 0 0 -1em, -2em -2em 0 -1em;}
              25%	{box-shadow: 0 -3em 0 -0.5em, 2em -2em 0 0, 3em 0 0 0.2em, 2em 2em 0 0, 0 3em 0 -1em, -2em 2em 0 -1em, -3em 0 0 -1em, -2em -2em 0 -1em;}
              37.5% {box-shadow: 0 -3em 0 -1em, 2em -2em 0 -1em, 3em 0em 0 0, 2em 2em 0 0.2em, 0 3em 0 0em, -2em 2em 0 -1em, -3em 0em 0 -1em, -2em -2em 0 -1em;}
              50%	{box-shadow: 0 -3em 0 -1em, 2em -2em 0 -1em, 3em 0 0 -1em, 2em 2em 0 0em, 0 3em 0 0.2em, -2em 2em 0 0, -3em 0em 0 -1em, -2em -2em 0 -1em;}
              62.5% {box-shadow: 0 -3em 0 -1em, 2em -2em 0 -1em, 3em 0 0 -1em, 2em 2em 0 -1em, 0 3em 0 0, -2em 2em 0 0.2em, -3em 0 0 0, -2em -2em 0 -1em;}
              75% 	{box-shadow: 0em -3em 0 -1em, 2em -2em 0 -1em, 3em 0em 0 -1em, 2em 2em 0 -1em, 0 3em 0 -1em, -2em 2em 0 0, -3em 0em 0 0.2em, -2em -2em 0 0;}
              87.5% {box-shadow: 0em -3em 0 0, 2em -2em 0 -1em, 3em 0 0 -1em, 2em 2em 0 -1em, 0 3em 0 -1em, -2em 2em 0 0, -3em 0em 0 0, -2em -2em 0 0.2em;}
            }
            @keyframes mg-circles-spinner {
              0%,
              100% 	{box-shadow: 0 -3em 0 0.2em, 2em -2em 0 0em, 3em 0 0 -1em, 2em 2em 0 -1em, 0 3em 0 -1em, -2em 2em 0 -1em, -3em 0 0 -1em, -2em -2em 0 0;}
              12.5% {box-shadow: 0 -3em 0 0, 2em -2em 0 0.2em, 3em 0 0 0, 2em 2em 0 -1em, 0 3em 0 -1em, -2em 2em 0 -1em, -3em 0 0 -1em, -2em -2em 0 -1em;}
              25% 	{box-shadow: 0 -3em 0 -0.5em, 2em -2em 0 0, 3em 0 0 0.2em, 2em 2em 0 0, 0 3em 0 -1em, -2em 2em 0 -1em, -3em 0 0 -1em, -2em -2em 0 -1em;}
              37.5% {box-shadow: 0 -3em 0 -1em, 2em -2em 0 -1em, 3em 0em 0 0, 2em 2em 0 0.2em, 0 3em 0 0em, -2em 2em 0 -1em, -3em 0em 0 -1em, -2em -2em 0 -1em;}
              50% 	{box-shadow: 0 -3em 0 -1em, 2em -2em 0 -1em, 3em 0 0 -1em, 2em 2em 0 0em, 0 3em 0 0.2em, -2em 2em 0 0, -3em 0em 0 -1em, -2em -2em 0 -1em;}
              62.5% {box-shadow: 0 -3em 0 -1em, 2em -2em 0 -1em, 3em 0 0 -1em, 2em 2em 0 -1em, 0 3em 0 0, -2em 2em 0 0.2em, -3em 0 0 0, -2em -2em 0 -1em;}
              75% 	{box-shadow: 0em -3em 0 -1em, 2em -2em 0 -1em, 3em 0em 0 -1em, 2em 2em 0 -1em, 0 3em 0 -1em, -2em 2em 0 0, -3em 0em 0 0.2em, -2em -2em 0 0;}
              87.5% {box-shadow: 0em -3em 0 0, 2em -2em 0 -1em, 3em 0 0 -1em, 2em 2em 0 -1em, 0 3em 0 -1em, -2em 2em 0 0, -3em 0em 0 0, -2em -2em 0 0.2em;}
            }
			<?php
			break;
			
			
			
		/* minimal spinner */
		case 'minimal_spinner':
			?>
            .mg_loader .mgl_1 {
            	border-color: <?php echo mg_static::hex2rgba($color, '.25').' '.mg_static::hex2rgba($color, '.25').' '.$color ?>;
            }
            #mg_lb_wrap .mgl_1 {
                border-color: <?php echo mg_static::hex2rgba($lb_color, '.25').' '.mg_static::hex2rgba($lb_color, '.25').' '.$lb_color ?>;
            }
			.mg_loader {
                width: 34px;
                height: 34px;
                margin-top: -17px;
                margin-left: -17px;	
            }
            #mg_lb_loader .mg_loader {
            	margin-top: -11px;
                margin-left: -11px;	
            
				-webkit-transform: scale(1.1);
                      transform: scale(1.1);
            }
            .mgl_1,
            .mgl_1:after {
                border-radius: 50%;
                box-sizing: border-box !important;	
                height: 100%;
            }
            #mg_lb_loader .mgl_1 {
                height: 22px;
                width: 22px; 	
            }
            .mgl_1 {
                background: none !important;
                font-size: 10px;
                border-size: 6px;
                border-style: solid;
                
                -webkit-animation: 	mg_minimal_spinner 1.05s infinite linear;
                animation: 			mg_minimal_spinner 1.05s infinite linear;
            }
            @-webkit-keyframes mg_minimal_spinner {
                0% {-webkit-transform: rotate(0deg);}
                100% {-webkit-transform: rotate(360deg);}
            }
            @keyframes mg_minimal_spinner {
                0% {transform: rotate(0deg);}
                100% {transform: rotate(360deg);}
            }
			<?php
			break;
			
			
			
		/* spotify-like spinner */
		case 'spotify_like':
			?>
            #mg_lb_loader .mg_loader {
              -webkit-transform: scale(0.9);
                      transform: scale(0.9);
            }
            .mgl_1 {
                background: none !important;
                border-radius: 50%;
                font-size: 5px;
                height: 28%;
                margin-left: 36%;
                margin-top: 36%;
                width: 28%;
            
                -webkit-animation: 	mg_spotify .9s infinite ease;
                animation: 			mg_spotify .9s infinite ease;
            }
            #mg_lb_wrap .mgl_1 {
                -webkit-animation-name: mg_spotify_lb;
    			animation-name: mg_spotify_lb;
            }
            
            @-webkit-keyframes mg_spotify {
              0%,
              100% {
                box-shadow: 0em -2.6em 0em 0em <?php echo $color ?>, 1.8em -1.8em 0 0em <?php echo mg_static::hex2rgba($color, '.2') ?>, 2.5em 0em 0 0em <?php echo mg_static::hex2rgba($color, '.2') ?>, 1.75em 1.75em 0 0em <?php echo mg_static::hex2rgba($color, '.2') ?>, 0em 2.5em 0 0em <?php echo mg_static::hex2rgba($color, '.2') ?>, -1.8em 1.8em 0 0em <?php echo mg_static::hex2rgba($color, '.2') ?>, -2.6em 0em 0 0em <?php echo mg_static::hex2rgba($color, '.5') ?>, -1.8em -1.8em 0 0em <?php echo mg_static::hex2rgba($color, '.7') ?>;
              }
              12.5% {
                box-shadow: 0em -2.6em 0em 0em <?php echo mg_static::hex2rgba($color, '.7') ?>, 1.8em -1.8em 0 0em <?php echo $color ?>, 2.5em 0em 0 0em <?php echo mg_static::hex2rgba($color, '.2') ?>, 1.75em 1.75em 0 0em <?php echo mg_static::hex2rgba($color, '.2') ?>, 0em 2.5em 0 0em <?php echo mg_static::hex2rgba($color, '.2') ?>, -1.8em 1.8em 0 0em <?php echo mg_static::hex2rgba($color, '.2') ?>, -2.6em 0em 0 0em <?php echo mg_static::hex2rgba($color, '.2') ?>, -1.8em -1.8em 0 0em <?php echo mg_static::hex2rgba($color, '.5') ?>;
              }
              25% {
                box-shadow: 0em -2.6em 0em 0em <?php echo mg_static::hex2rgba($color, '.5') ?>, 1.8em -1.8em 0 0em <?php echo mg_static::hex2rgba($color, '.7') ?>, 2.5em 0em 0 0em <?php echo $color ?>, 1.75em 1.75em 0 0em <?php echo mg_static::hex2rgba($color, '.2') ?>, 0em 2.5em 0 0em <?php echo mg_static::hex2rgba($color, '.2') ?>, -1.8em 1.8em 0 0em <?php echo mg_static::hex2rgba($color, '.2') ?>, -2.6em 0em 0 0em <?php echo mg_static::hex2rgba($color, '.2') ?>, -1.8em -1.8em 0 0em <?php echo mg_static::hex2rgba($color, '.2') ?>;
              }
              37.5% {
                box-shadow: 0em -2.6em 0em 0em <?php echo mg_static::hex2rgba($color, '.2') ?>, 1.8em -1.8em 0 0em <?php echo mg_static::hex2rgba($color, '.5') ?>, 2.5em 0em 0 0em <?php echo mg_static::hex2rgba($color, '.7') ?>, 1.75em 1.75em 0 0em <?php echo $color ?>, 0em 2.5em 0 0em <?php echo mg_static::hex2rgba($color, '.2') ?>, -1.8em 1.8em 0 0em <?php echo mg_static::hex2rgba($color, '.2') ?>, -2.6em 0em 0 0em <?php echo mg_static::hex2rgba($color, '.2') ?>, -1.8em -1.8em 0 0em <?php echo mg_static::hex2rgba($color, '.2') ?>;
              }
              50% {
                box-shadow: 0em -2.6em 0em 0em <?php echo mg_static::hex2rgba($color, '.2') ?>, 1.8em -1.8em 0 0em <?php echo mg_static::hex2rgba($color, '.2') ?>, 2.5em 0em 0 0em <?php echo mg_static::hex2rgba($color, '.5') ?>, 1.75em 1.75em 0 0em <?php echo mg_static::hex2rgba($color, '.7') ?>, 0em 2.5em 0 0em <?php echo $color ?>, -1.8em 1.8em 0 0em <?php echo mg_static::hex2rgba($color, '.2') ?>, -2.6em 0em 0 0em <?php echo mg_static::hex2rgba($color, '.2') ?>, -1.8em -1.8em 0 0em <?php echo mg_static::hex2rgba($color, '.2') ?>;
              }
              62.5% {
                box-shadow: 0em -2.6em 0em 0em <?php echo mg_static::hex2rgba($color, '.2') ?>, 1.8em -1.8em 0 0em <?php echo mg_static::hex2rgba($color, '.2') ?>, 2.5em 0em 0 0em <?php echo mg_static::hex2rgba($color, '.2') ?>, 1.75em 1.75em 0 0em <?php echo mg_static::hex2rgba($color, '.5') ?>, 0em 2.5em 0 0em <?php echo mg_static::hex2rgba($color, '.7') ?>, -1.8em 1.8em 0 0em <?php echo $color ?>, -2.6em 0em 0 0em <?php echo mg_static::hex2rgba($color, '.2') ?>, -1.8em -1.8em 0 0em <?php echo mg_static::hex2rgba($color, '.2') ?>;
              }
              75% {
                box-shadow: 0em -2.6em 0em 0em <?php echo mg_static::hex2rgba($color, '.2') ?>, 1.8em -1.8em 0 0em <?php echo mg_static::hex2rgba($color, '.2') ?>, 2.5em 0em 0 0em <?php echo mg_static::hex2rgba($color, '.2') ?>, 1.75em 1.75em 0 0em <?php echo mg_static::hex2rgba($color, '.2') ?>, 0em 2.5em 0 0em <?php echo mg_static::hex2rgba($color, '.5') ?>, -1.8em 1.8em 0 0em <?php echo mg_static::hex2rgba($color, '.7') ?>, -2.6em 0em 0 0em <?php echo $color ?>, -1.8em -1.8em 0 0em <?php echo mg_static::hex2rgba($color, '.2') ?>;
              }
              87.5% {
                box-shadow: 0em -2.6em 0em 0em <?php echo mg_static::hex2rgba($color, '.2') ?>, 1.8em -1.8em 0 0em <?php echo mg_static::hex2rgba($color, '.2') ?>, 2.5em 0em 0 0em <?php echo mg_static::hex2rgba($color, '.2') ?>, 1.75em 1.75em 0 0em <?php echo mg_static::hex2rgba($color, '.2') ?>, 0em 2.5em 0 0em <?php echo mg_static::hex2rgba($color, '.2') ?>, -1.8em 1.8em 0 0em <?php echo mg_static::hex2rgba($color, '.5') ?>, -2.6em 0em 0 0em <?php echo mg_static::hex2rgba($color, '.7') ?>, -1.8em -1.8em 0 0em <?php echo $color ?>;
              }
            }
            @keyframes mg_spotify {
              0%,
              100% {
                box-shadow: 0em -2.6em 0em 0em <?php echo $color ?>, 1.8em -1.8em 0 0em <?php echo mg_static::hex2rgba($color, '.2') ?>, 2.5em 0em 0 0em <?php echo mg_static::hex2rgba($color, '.2') ?>, 1.75em 1.75em 0 0em <?php echo mg_static::hex2rgba($color, '.2') ?>, 0em 2.5em 0 0em <?php echo mg_static::hex2rgba($color, '.2') ?>, -1.8em 1.8em 0 0em <?php echo mg_static::hex2rgba($color, '.2') ?>, -2.6em 0em 0 0em <?php echo mg_static::hex2rgba($color, '.5') ?>, -1.8em -1.8em 0 0em <?php echo mg_static::hex2rgba($color, '.7') ?>;
              }
              12.5% {
                box-shadow: 0em -2.6em 0em 0em <?php echo mg_static::hex2rgba($color, '.7') ?>, 1.8em -1.8em 0 0em <?php echo $color ?>, 2.5em 0em 0 0em <?php echo mg_static::hex2rgba($color, '.2') ?>, 1.75em 1.75em 0 0em <?php echo mg_static::hex2rgba($color, '.2') ?>, 0em 2.5em 0 0em <?php echo mg_static::hex2rgba($color, '.2') ?>, -1.8em 1.8em 0 0em <?php echo mg_static::hex2rgba($color, '.2') ?>, -2.6em 0em 0 0em <?php echo mg_static::hex2rgba($color, '.2') ?>, -1.8em -1.8em 0 0em <?php echo mg_static::hex2rgba($color, '.5') ?>;
              }
              25% {
                box-shadow: 0em -2.6em 0em 0em <?php echo mg_static::hex2rgba($color, '.5') ?>, 1.8em -1.8em 0 0em <?php echo mg_static::hex2rgba($color, '.7') ?>, 2.5em 0em 0 0em <?php echo $color ?>, 1.75em 1.75em 0 0em <?php echo mg_static::hex2rgba($color, '.2') ?>, 0em 2.5em 0 0em <?php echo mg_static::hex2rgba($color, '.2') ?>, -1.8em 1.8em 0 0em <?php echo mg_static::hex2rgba($color, '.2') ?>, -2.6em 0em 0 0em <?php echo mg_static::hex2rgba($color, '.2') ?>, -1.8em -1.8em 0 0em <?php echo mg_static::hex2rgba($color, '.2') ?>;
              }
              37.5% {
                box-shadow: 0em -2.6em 0em 0em <?php echo mg_static::hex2rgba($color, '.2') ?>, 1.8em -1.8em 0 0em <?php echo mg_static::hex2rgba($color, '.5') ?>, 2.5em 0em 0 0em <?php echo mg_static::hex2rgba($color, '.7') ?>, 1.75em 1.75em 0 0em <?php echo $color ?>, 0em 2.5em 0 0em <?php echo mg_static::hex2rgba($color, '.2') ?>, -1.8em 1.8em 0 0em <?php echo mg_static::hex2rgba($color, '.2') ?>, -2.6em 0em 0 0em <?php echo mg_static::hex2rgba($color, '.2') ?>, -1.8em -1.8em 0 0em <?php echo mg_static::hex2rgba($color, '.2') ?>;
              }
              50% {
                box-shadow: 0em -2.6em 0em 0em <?php echo mg_static::hex2rgba($color, '.2') ?>, 1.8em -1.8em 0 0em <?php echo mg_static::hex2rgba($color, '.2') ?>, 2.5em 0em 0 0em <?php echo mg_static::hex2rgba($color, '.5') ?>, 1.75em 1.75em 0 0em <?php echo mg_static::hex2rgba($color, '.7') ?>, 0em 2.5em 0 0em <?php echo $color ?>, -1.8em 1.8em 0 0em <?php echo mg_static::hex2rgba($color, '.2') ?>, -2.6em 0em 0 0em <?php echo mg_static::hex2rgba($color, '.2') ?>, -1.8em -1.8em 0 0em <?php echo mg_static::hex2rgba($color, '.2') ?>;
              }
              62.5% {
                box-shadow: 0em -2.6em 0em 0em <?php echo mg_static::hex2rgba($color, '.2') ?>, 1.8em -1.8em 0 0em <?php echo mg_static::hex2rgba($color, '.2') ?>, 2.5em 0em 0 0em <?php echo mg_static::hex2rgba($color, '.2') ?>, 1.75em 1.75em 0 0em <?php echo mg_static::hex2rgba($color, '.5') ?>, 0em 2.5em 0 0em <?php echo mg_static::hex2rgba($color, '.7') ?>, -1.8em 1.8em 0 0em <?php echo $color ?>, -2.6em 0em 0 0em <?php echo mg_static::hex2rgba($color, '.2') ?>, -1.8em -1.8em 0 0em <?php echo mg_static::hex2rgba($color, '.2') ?>;
              }
              75% {
                box-shadow: 0em -2.6em 0em 0em <?php echo mg_static::hex2rgba($color, '.2') ?>, 1.8em -1.8em 0 0em <?php echo mg_static::hex2rgba($color, '.2') ?>, 2.5em 0em 0 0em <?php echo mg_static::hex2rgba($color, '.2') ?>, 1.75em 1.75em 0 0em <?php echo mg_static::hex2rgba($color, '.2') ?>, 0em 2.5em 0 0em <?php echo mg_static::hex2rgba($color, '.5') ?>, -1.8em 1.8em 0 0em <?php echo mg_static::hex2rgba($color, '.7') ?>, -2.6em 0em 0 0em <?php echo $color ?>, -1.8em -1.8em 0 0em <?php echo mg_static::hex2rgba($color, '.2') ?>;
              }
              87.5% {
                box-shadow: 0em -2.6em 0em 0em <?php echo mg_static::hex2rgba($color, '.2') ?>, 1.8em -1.8em 0 0em <?php echo mg_static::hex2rgba($color, '.2') ?>, 2.5em 0em 0 0em <?php echo mg_static::hex2rgba($color, '.2') ?>, 1.75em 1.75em 0 0em <?php echo mg_static::hex2rgba($color, '.2') ?>, 0em 2.5em 0 0em <?php echo mg_static::hex2rgba($color, '.2') ?>, -1.8em 1.8em 0 0em <?php echo mg_static::hex2rgba($color, '.5') ?>, -2.6em 0em 0 0em <?php echo mg_static::hex2rgba($color, '.7') ?>, -1.8em -1.8em 0 0em <?php echo $color ?>;
              }
            }
            
            @-webkit-keyframes mg_spotify_lb {
              0%,
              100% {
                box-shadow: 0em -2.6em 0em 0em <?php echo $lb_color ?>, 1.8em -1.8em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.2') ?>, 2.5em 0em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.2') ?>, 1.75em 1.75em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.2') ?>, 0em 2.5em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.2') ?>, -1.8em 1.8em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.2') ?>, -2.6em 0em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.5') ?>, -1.8em -1.8em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.7') ?>;
              }
              12.5% {
                box-shadow: 0em -2.6em 0em 0em <?php echo mg_static::hex2rgba($lb_color, '.7') ?>, 1.8em -1.8em 0 0em <?php echo $lb_color ?>, 2.5em 0em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.2') ?>, 1.75em 1.75em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.2') ?>, 0em 2.5em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.2') ?>, -1.8em 1.8em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.2') ?>, -2.6em 0em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.2') ?>, -1.8em -1.8em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.5') ?>;
              }
              25% {
                box-shadow: 0em -2.6em 0em 0em <?php echo mg_static::hex2rgba($lb_color, '.5') ?>, 1.8em -1.8em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.7') ?>, 2.5em 0em 0 0em <?php echo $lb_color ?>, 1.75em 1.75em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.2') ?>, 0em 2.5em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.2') ?>, -1.8em 1.8em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.2') ?>, -2.6em 0em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.2') ?>, -1.8em -1.8em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.2') ?>;
              }
              37.5% {
                box-shadow: 0em -2.6em 0em 0em <?php echo mg_static::hex2rgba($lb_color, '.2') ?>, 1.8em -1.8em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.5') ?>, 2.5em 0em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.7') ?>, 1.75em 1.75em 0 0em <?php echo $lb_color ?>, 0em 2.5em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.2') ?>, -1.8em 1.8em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.2') ?>, -2.6em 0em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.2') ?>, -1.8em -1.8em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.2') ?>;
              }
              50% {
                box-shadow: 0em -2.6em 0em 0em <?php echo mg_static::hex2rgba($lb_color, '.2') ?>, 1.8em -1.8em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.2') ?>, 2.5em 0em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.5') ?>, 1.75em 1.75em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.7') ?>, 0em 2.5em 0 0em <?php echo $lb_color ?>, -1.8em 1.8em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.2') ?>, -2.6em 0em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.2') ?>, -1.8em -1.8em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.2') ?>;
              }
              62.5% {
                box-shadow: 0em -2.6em 0em 0em <?php echo mg_static::hex2rgba($lb_color, '.2') ?>, 1.8em -1.8em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.2') ?>, 2.5em 0em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.2') ?>, 1.75em 1.75em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.5') ?>, 0em 2.5em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.7') ?>, -1.8em 1.8em 0 0em <?php echo $lb_color ?>, -2.6em 0em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.2') ?>, -1.8em -1.8em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.2') ?>;
              }
              75% {
                box-shadow: 0em -2.6em 0em 0em <?php echo mg_static::hex2rgba($lb_color, '.2') ?>, 1.8em -1.8em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.2') ?>, 2.5em 0em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.2') ?>, 1.75em 1.75em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.2') ?>, 0em 2.5em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.5') ?>, -1.8em 1.8em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.7') ?>, -2.6em 0em 0 0em <?php echo $lb_color ?>, -1.8em -1.8em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.2') ?>;
              }
              87.5% {
                box-shadow: 0em -2.6em 0em 0em <?php echo mg_static::hex2rgba($lb_color, '.2') ?>, 1.8em -1.8em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.2') ?>, 2.5em 0em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.2') ?>, 1.75em 1.75em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.2') ?>, 0em 2.5em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.2') ?>, -1.8em 1.8em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.5') ?>, -2.6em 0em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.7') ?>, -1.8em -1.8em 0 0em <?php echo $lb_color ?>;
              }
            }
            @keyframes mg_spotify_lb {
              0%,
              100% {
                box-shadow: 0em -2.6em 0em 0em <?php echo $lb_color ?>, 1.8em -1.8em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.2') ?>, 2.5em 0em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.2') ?>, 1.75em 1.75em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.2') ?>, 0em 2.5em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.2') ?>, -1.8em 1.8em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.2') ?>, -2.6em 0em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.5') ?>, -1.8em -1.8em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.7') ?>;
              }
              12.5% {
                box-shadow: 0em -2.6em 0em 0em <?php echo mg_static::hex2rgba($lb_color, '.7') ?>, 1.8em -1.8em 0 0em <?php echo $lb_color ?>, 2.5em 0em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.2') ?>, 1.75em 1.75em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.2') ?>, 0em 2.5em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.2') ?>, -1.8em 1.8em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.2') ?>, -2.6em 0em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.2') ?>, -1.8em -1.8em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.5') ?>;
              }
              25% {
                box-shadow: 0em -2.6em 0em 0em <?php echo mg_static::hex2rgba($lb_color, '.5') ?>, 1.8em -1.8em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.7') ?>, 2.5em 0em 0 0em <?php echo $lb_color ?>, 1.75em 1.75em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.2') ?>, 0em 2.5em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.2') ?>, -1.8em 1.8em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.2') ?>, -2.6em 0em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.2') ?>, -1.8em -1.8em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.2') ?>;
              }
              37.5% {
                box-shadow: 0em -2.6em 0em 0em <?php echo mg_static::hex2rgba($lb_color, '.2') ?>, 1.8em -1.8em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.5') ?>, 2.5em 0em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.7') ?>, 1.75em 1.75em 0 0em <?php echo $lb_color ?>, 0em 2.5em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.2') ?>, -1.8em 1.8em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.2') ?>, -2.6em 0em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.2') ?>, -1.8em -1.8em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.2') ?>;
              }
              50% {
                box-shadow: 0em -2.6em 0em 0em <?php echo mg_static::hex2rgba($lb_color, '.2') ?>, 1.8em -1.8em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.2') ?>, 2.5em 0em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.5') ?>, 1.75em 1.75em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.7') ?>, 0em 2.5em 0 0em <?php echo $lb_color ?>, -1.8em 1.8em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.2') ?>, -2.6em 0em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.2') ?>, -1.8em -1.8em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.2') ?>;
              }
              62.5% {
                box-shadow: 0em -2.6em 0em 0em <?php echo mg_static::hex2rgba($lb_color, '.2') ?>, 1.8em -1.8em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.2') ?>, 2.5em 0em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.2') ?>, 1.75em 1.75em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.5') ?>, 0em 2.5em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.7') ?>, -1.8em 1.8em 0 0em <?php echo $lb_color ?>, -2.6em 0em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.2') ?>, -1.8em -1.8em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.2') ?>;
              }
              75% {
                box-shadow: 0em -2.6em 0em 0em <?php echo mg_static::hex2rgba($lb_color, '.2') ?>, 1.8em -1.8em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.2') ?>, 2.5em 0em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.2') ?>, 1.75em 1.75em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.2') ?>, 0em 2.5em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.5') ?>, -1.8em 1.8em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.7') ?>, -2.6em 0em 0 0em <?php echo $lb_color ?>, -1.8em -1.8em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.2') ?>;
              }
              87.5% {
                box-shadow: 0em -2.6em 0em 0em <?php echo mg_static::hex2rgba($lb_color, '.2') ?>, 1.8em -1.8em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.2') ?>, 2.5em 0em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.2') ?>, 1.75em 1.75em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.2') ?>, 0em 2.5em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.2') ?>, -1.8em 1.8em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.5') ?>, -2.6em 0em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.7') ?>, -1.8em -1.8em 0 0em <?php echo $lb_color ?>;
              }
            }
			<?php
			break;
		
		
		
		/* minimal spinner */
		case 'vortex':
			?>
            #mg_lb_loader .mg_loader {
              -webkit-transform: scale(0.9);
                      transform: scale(0.9);
            }
            .mgl_1 {
                background: none !important;
                border-radius: 50%;
                font-size: 3px;
                height: 70%;
                margin-left: 15%;
                margin-top: 15%;
                width: 70%;
              
                -webkit-animation:	mg_vortex .45s infinite linear;
                animation: 			mg_vortex .45s infinite linear;
            }
            #mg_lb_wrap .mgl_1 {
                -webkit-animation-name: mg_vortex_lb;
    			animation-name: mg_vortex_lb;
            }
            
            @-webkit-keyframes mg_vortex {
              0%,
              100% {
                box-shadow: 0em -2.6em 0em 0em <?php echo $color ?>, 1.8em -1.8em 0 0em <?php echo mg_static::hex2rgba($color, '.2') ?>, 2.5em 0em 0 0em <?php echo mg_static::hex2rgba($color, '.2') ?>, 1.75em 1.75em 0 0em <?php echo mg_static::hex2rgba($color, '.2') ?>, 0em 2.5em 0 0em <?php echo mg_static::hex2rgba($color, '.2') ?>, -1.8em 1.8em 0 0em <?php echo mg_static::hex2rgba($color, '.2') ?>, -2.6em 0em 0 0em <?php echo mg_static::hex2rgba($color, '.5') ?>, -1.8em -1.8em 0 0em <?php echo mg_static::hex2rgba($color, '.7') ?>;
              }
              12.5% {
                box-shadow: 0em -2.6em 0em 0em <?php echo mg_static::hex2rgba($color, '.7') ?>, 1.8em -1.8em 0 0em <?php echo $color ?>, 2.5em 0em 0 0em <?php echo mg_static::hex2rgba($color, '.2') ?>, 1.75em 1.75em 0 0em <?php echo mg_static::hex2rgba($color, '.2') ?>, 0em 2.5em 0 0em <?php echo mg_static::hex2rgba($color, '.2') ?>, -1.8em 1.8em 0 0em <?php echo mg_static::hex2rgba($color, '.2') ?>, -2.6em 0em 0 0em <?php echo mg_static::hex2rgba($color, '.2') ?>, -1.8em -1.8em 0 0em <?php echo mg_static::hex2rgba($color, '.5') ?>;
              }
              25% {
                box-shadow: 0em -2.6em 0em 0em <?php echo mg_static::hex2rgba($color, '.5') ?>, 1.8em -1.8em 0 0em <?php echo mg_static::hex2rgba($color, '.7') ?>, 2.5em 0em 0 0em <?php echo $color ?>, 1.75em 1.75em 0 0em <?php echo mg_static::hex2rgba($color, '.2') ?>, 0em 2.5em 0 0em <?php echo mg_static::hex2rgba($color, '.2') ?>, -1.8em 1.8em 0 0em <?php echo mg_static::hex2rgba($color, '.2') ?>, -2.6em 0em 0 0em <?php echo mg_static::hex2rgba($color, '.2') ?>, -1.8em -1.8em 0 0em <?php echo mg_static::hex2rgba($color, '.2') ?>;
              }
              37.5% {
                box-shadow: 0em -2.6em 0em 0em <?php echo mg_static::hex2rgba($color, '.2') ?>, 1.8em -1.8em 0 0em <?php echo mg_static::hex2rgba($color, '.5') ?>, 2.5em 0em 0 0em <?php echo mg_static::hex2rgba($color, '.7') ?>, 1.75em 1.75em 0 0em <?php echo $color ?>, 0em 2.5em 0 0em <?php echo mg_static::hex2rgba($color, '.2') ?>, -1.8em 1.8em 0 0em <?php echo mg_static::hex2rgba($color, '.2') ?>, -2.6em 0em 0 0em <?php echo mg_static::hex2rgba($color, '.2') ?>, -1.8em -1.8em 0 0em <?php echo mg_static::hex2rgba($color, '.2') ?>;
              }
              50% {
                box-shadow: 0em -2.6em 0em 0em <?php echo mg_static::hex2rgba($color, '.2') ?>, 1.8em -1.8em 0 0em <?php echo mg_static::hex2rgba($color, '.2') ?>, 2.5em 0em 0 0em <?php echo mg_static::hex2rgba($color, '.5') ?>, 1.75em 1.75em 0 0em <?php echo mg_static::hex2rgba($color, '.7') ?>, 0em 2.5em 0 0em <?php echo $color ?>, -1.8em 1.8em 0 0em <?php echo mg_static::hex2rgba($color, '.2') ?>, -2.6em 0em 0 0em <?php echo mg_static::hex2rgba($color, '.2') ?>, -1.8em -1.8em 0 0em <?php echo mg_static::hex2rgba($color, '.2') ?>;
              }
              62.5% {
                box-shadow: 0em -2.6em 0em 0em <?php echo mg_static::hex2rgba($color, '.2') ?>, 1.8em -1.8em 0 0em <?php echo mg_static::hex2rgba($color, '.2') ?>, 2.5em 0em 0 0em <?php echo mg_static::hex2rgba($color, '.2') ?>, 1.75em 1.75em 0 0em <?php echo mg_static::hex2rgba($color, '.5') ?>, 0em 2.5em 0 0em <?php echo mg_static::hex2rgba($color, '.7') ?>, -1.8em 1.8em 0 0em <?php echo $color ?>, -2.6em 0em 0 0em <?php echo mg_static::hex2rgba($color, '.2') ?>, -1.8em -1.8em 0 0em <?php echo mg_static::hex2rgba($color, '.2') ?>;
              }
              75% {
                box-shadow: 0em -2.6em 0em 0em <?php echo mg_static::hex2rgba($color, '.2') ?>, 1.8em -1.8em 0 0em <?php echo mg_static::hex2rgba($color, '.2') ?>, 2.5em 0em 0 0em <?php echo mg_static::hex2rgba($color, '.2') ?>, 1.75em 1.75em 0 0em <?php echo mg_static::hex2rgba($color, '.2') ?>, 0em 2.5em 0 0em <?php echo mg_static::hex2rgba($color, '.5') ?>, -1.8em 1.8em 0 0em <?php echo mg_static::hex2rgba($color, '.7') ?>, -2.6em 0em 0 0em <?php echo $color ?>, -1.8em -1.8em 0 0em <?php echo mg_static::hex2rgba($color, '.2') ?>;
              }
              87.5% {
                box-shadow: 0em -2.6em 0em 0em <?php echo mg_static::hex2rgba($color, '.2') ?>, 1.8em -1.8em 0 0em <?php echo mg_static::hex2rgba($color, '.2') ?>, 2.5em 0em 0 0em <?php echo mg_static::hex2rgba($color, '.2') ?>, 1.75em 1.75em 0 0em <?php echo mg_static::hex2rgba($color, '.2') ?>, 0em 2.5em 0 0em <?php echo mg_static::hex2rgba($color, '.2') ?>, -1.8em 1.8em 0 0em <?php echo mg_static::hex2rgba($color, '.5') ?>, -2.6em 0em 0 0em <?php echo mg_static::hex2rgba($color, '.7') ?>, -1.8em -1.8em 0 0em <?php echo $color ?>;
              }
            }
            @keyframes mg_vortex {
              0%,
              100% {
                box-shadow: 0em -2.6em 0em 0em <?php echo $color ?>, 1.8em -1.8em 0 0em <?php echo mg_static::hex2rgba($color, '.2') ?>, 2.5em 0em 0 0em <?php echo mg_static::hex2rgba($color, '.2') ?>, 1.75em 1.75em 0 0em <?php echo mg_static::hex2rgba($color, '.2') ?>, 0em 2.5em 0 0em <?php echo mg_static::hex2rgba($color, '.2') ?>, -1.8em 1.8em 0 0em <?php echo mg_static::hex2rgba($color, '.2') ?>, -2.6em 0em 0 0em <?php echo mg_static::hex2rgba($color, '.5') ?>, -1.8em -1.8em 0 0em <?php echo mg_static::hex2rgba($color, '.7') ?>;
              }
              12.5% {
                box-shadow: 0em -2.6em 0em 0em <?php echo mg_static::hex2rgba($color, '.7') ?>, 1.8em -1.8em 0 0em <?php echo $color ?>, 2.5em 0em 0 0em <?php echo mg_static::hex2rgba($color, '.2') ?>, 1.75em 1.75em 0 0em <?php echo mg_static::hex2rgba($color, '.2') ?>, 0em 2.5em 0 0em <?php echo mg_static::hex2rgba($color, '.2') ?>, -1.8em 1.8em 0 0em <?php echo mg_static::hex2rgba($color, '.2') ?>, -2.6em 0em 0 0em <?php echo mg_static::hex2rgba($color, '.2') ?>, -1.8em -1.8em 0 0em <?php echo mg_static::hex2rgba($color, '.5') ?>;
              }
              25% {
                box-shadow: 0em -2.6em 0em 0em <?php echo mg_static::hex2rgba($color, '.5') ?>, 1.8em -1.8em 0 0em <?php echo mg_static::hex2rgba($color, '.7') ?>, 2.5em 0em 0 0em <?php echo $color ?>, 1.75em 1.75em 0 0em <?php echo mg_static::hex2rgba($color, '.2') ?>, 0em 2.5em 0 0em <?php echo mg_static::hex2rgba($color, '.2') ?>, -1.8em 1.8em 0 0em <?php echo mg_static::hex2rgba($color, '.2') ?>, -2.6em 0em 0 0em <?php echo mg_static::hex2rgba($color, '.2') ?>, -1.8em -1.8em 0 0em <?php echo mg_static::hex2rgba($color, '.2') ?>;
              }
              37.5% {
                box-shadow: 0em -2.6em 0em 0em <?php echo mg_static::hex2rgba($color, '.2') ?>, 1.8em -1.8em 0 0em <?php echo mg_static::hex2rgba($color, '.5') ?>, 2.5em 0em 0 0em <?php echo mg_static::hex2rgba($color, '.7') ?>, 1.75em 1.75em 0 0em <?php echo $color ?>, 0em 2.5em 0 0em <?php echo mg_static::hex2rgba($color, '.2') ?>, -1.8em 1.8em 0 0em <?php echo mg_static::hex2rgba($color, '.2') ?>, -2.6em 0em 0 0em <?php echo mg_static::hex2rgba($color, '.2') ?>, -1.8em -1.8em 0 0em <?php echo mg_static::hex2rgba($color, '.2') ?>;
              }
              50% {
                box-shadow: 0em -2.6em 0em 0em <?php echo mg_static::hex2rgba($color, '.2') ?>, 1.8em -1.8em 0 0em <?php echo mg_static::hex2rgba($color, '.2') ?>, 2.5em 0em 0 0em <?php echo mg_static::hex2rgba($color, '.5') ?>, 1.75em 1.75em 0 0em <?php echo mg_static::hex2rgba($color, '.7') ?>, 0em 2.5em 0 0em <?php echo $color ?>, -1.8em 1.8em 0 0em <?php echo mg_static::hex2rgba($color, '.2') ?>, -2.6em 0em 0 0em <?php echo mg_static::hex2rgba($color, '.2') ?>, -1.8em -1.8em 0 0em <?php echo mg_static::hex2rgba($color, '.2') ?>;
              }
              62.5% {
                box-shadow: 0em -2.6em 0em 0em <?php echo mg_static::hex2rgba($color, '.2') ?>, 1.8em -1.8em 0 0em <?php echo mg_static::hex2rgba($color, '.2') ?>, 2.5em 0em 0 0em <?php echo mg_static::hex2rgba($color, '.2') ?>, 1.75em 1.75em 0 0em <?php echo mg_static::hex2rgba($color, '.5') ?>, 0em 2.5em 0 0em <?php echo mg_static::hex2rgba($color, '.7') ?>, -1.8em 1.8em 0 0em <?php echo $color ?>, -2.6em 0em 0 0em <?php echo mg_static::hex2rgba($color, '.2') ?>, -1.8em -1.8em 0 0em <?php echo mg_static::hex2rgba($color, '.2') ?>;
              }
              75% {
                box-shadow: 0em -2.6em 0em 0em <?php echo mg_static::hex2rgba($color, '.2') ?>, 1.8em -1.8em 0 0em <?php echo mg_static::hex2rgba($color, '.2') ?>, 2.5em 0em 0 0em <?php echo mg_static::hex2rgba($color, '.2') ?>, 1.75em 1.75em 0 0em <?php echo mg_static::hex2rgba($color, '.2') ?>, 0em 2.5em 0 0em <?php echo mg_static::hex2rgba($color, '.5') ?>, -1.8em 1.8em 0 0em <?php echo mg_static::hex2rgba($color, '.7') ?>, -2.6em 0em 0 0em <?php echo $color ?>, -1.8em -1.8em 0 0em <?php echo mg_static::hex2rgba($color, '.2') ?>;
              }
              87.5% {
                box-shadow: 0em -2.6em 0em 0em <?php echo mg_static::hex2rgba($color, '.2') ?>, 1.8em -1.8em 0 0em <?php echo mg_static::hex2rgba($color, '.2') ?>, 2.5em 0em 0 0em <?php echo mg_static::hex2rgba($color, '.2') ?>, 1.75em 1.75em 0 0em <?php echo mg_static::hex2rgba($color, '.2') ?>, 0em 2.5em 0 0em <?php echo mg_static::hex2rgba($color, '.2') ?>, -1.8em 1.8em 0 0em <?php echo mg_static::hex2rgba($color, '.5') ?>, -2.6em 0em 0 0em <?php echo mg_static::hex2rgba($color, '.7') ?>, -1.8em -1.8em 0 0em <?php echo $color ?>;
              }
            }
            
            @-webkit-keyframes mg_vortex_lb {
              0%,
              100% {
                box-shadow: 0em -2.6em 0em 0em <?php echo $lb_color ?>, 1.8em -1.8em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.2') ?>, 2.5em 0em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.2') ?>, 1.75em 1.75em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.2') ?>, 0em 2.5em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.2') ?>, -1.8em 1.8em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.2') ?>, -2.6em 0em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.5') ?>, -1.8em -1.8em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.7') ?>;
              }
              12.5% {
                box-shadow: 0em -2.6em 0em 0em <?php echo mg_static::hex2rgba($lb_color, '.7') ?>, 1.8em -1.8em 0 0em <?php echo $lb_color ?>, 2.5em 0em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.2') ?>, 1.75em 1.75em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.2') ?>, 0em 2.5em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.2') ?>, -1.8em 1.8em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.2') ?>, -2.6em 0em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.2') ?>, -1.8em -1.8em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.5') ?>;
              }
              25% {
                box-shadow: 0em -2.6em 0em 0em <?php echo mg_static::hex2rgba($lb_color, '.5') ?>, 1.8em -1.8em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.7') ?>, 2.5em 0em 0 0em <?php echo $lb_color ?>, 1.75em 1.75em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.2') ?>, 0em 2.5em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.2') ?>, -1.8em 1.8em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.2') ?>, -2.6em 0em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.2') ?>, -1.8em -1.8em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.2') ?>;
              }
              37.5% {
                box-shadow: 0em -2.6em 0em 0em <?php echo mg_static::hex2rgba($lb_color, '.2') ?>, 1.8em -1.8em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.5') ?>, 2.5em 0em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.7') ?>, 1.75em 1.75em 0 0em <?php echo $lb_color ?>, 0em 2.5em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.2') ?>, -1.8em 1.8em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.2') ?>, -2.6em 0em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.2') ?>, -1.8em -1.8em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.2') ?>;
              }
              50% {
                box-shadow: 0em -2.6em 0em 0em <?php echo mg_static::hex2rgba($lb_color, '.2') ?>, 1.8em -1.8em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.2') ?>, 2.5em 0em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.5') ?>, 1.75em 1.75em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.7') ?>, 0em 2.5em 0 0em <?php echo $lb_color ?>, -1.8em 1.8em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.2') ?>, -2.6em 0em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.2') ?>, -1.8em -1.8em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.2') ?>;
              }
              62.5% {
                box-shadow: 0em -2.6em 0em 0em <?php echo mg_static::hex2rgba($lb_color, '.2') ?>, 1.8em -1.8em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.2') ?>, 2.5em 0em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.2') ?>, 1.75em 1.75em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.5') ?>, 0em 2.5em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.7') ?>, -1.8em 1.8em 0 0em <?php echo $lb_color ?>, -2.6em 0em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.2') ?>, -1.8em -1.8em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.2') ?>;
              }
              75% {
                box-shadow: 0em -2.6em 0em 0em <?php echo mg_static::hex2rgba($lb_color, '.2') ?>, 1.8em -1.8em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.2') ?>, 2.5em 0em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.2') ?>, 1.75em 1.75em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.2') ?>, 0em 2.5em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.5') ?>, -1.8em 1.8em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.7') ?>, -2.6em 0em 0 0em <?php echo $lb_color ?>, -1.8em -1.8em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.2') ?>;
              }
              87.5% {
                box-shadow: 0em -2.6em 0em 0em <?php echo mg_static::hex2rgba($lb_color, '.2') ?>, 1.8em -1.8em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.2') ?>, 2.5em 0em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.2') ?>, 1.75em 1.75em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.2') ?>, 0em 2.5em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.2') ?>, -1.8em 1.8em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.5') ?>, -2.6em 0em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.7') ?>, -1.8em -1.8em 0 0em <?php echo $lb_color ?>;
              }
            }
            @keyframes mg_vortex_lb {
              0%,
              100% {
                box-shadow: 0em -2.6em 0em 0em <?php echo $lb_color ?>, 1.8em -1.8em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.2') ?>, 2.5em 0em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.2') ?>, 1.75em 1.75em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.2') ?>, 0em 2.5em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.2') ?>, -1.8em 1.8em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.2') ?>, -2.6em 0em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.5') ?>, -1.8em -1.8em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.7') ?>;
              }
              12.5% {
                box-shadow: 0em -2.6em 0em 0em <?php echo mg_static::hex2rgba($lb_color, '.7') ?>, 1.8em -1.8em 0 0em <?php echo $lb_color ?>, 2.5em 0em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.2') ?>, 1.75em 1.75em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.2') ?>, 0em 2.5em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.2') ?>, -1.8em 1.8em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.2') ?>, -2.6em 0em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.2') ?>, -1.8em -1.8em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.5') ?>;
              }
              25% {
                box-shadow: 0em -2.6em 0em 0em <?php echo mg_static::hex2rgba($lb_color, '.5') ?>, 1.8em -1.8em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.7') ?>, 2.5em 0em 0 0em <?php echo $lb_color ?>, 1.75em 1.75em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.2') ?>, 0em 2.5em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.2') ?>, -1.8em 1.8em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.2') ?>, -2.6em 0em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.2') ?>, -1.8em -1.8em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.2') ?>;
              }
              37.5% {
                box-shadow: 0em -2.6em 0em 0em <?php echo mg_static::hex2rgba($lb_color, '.2') ?>, 1.8em -1.8em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.5') ?>, 2.5em 0em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.7') ?>, 1.75em 1.75em 0 0em <?php echo $lb_color ?>, 0em 2.5em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.2') ?>, -1.8em 1.8em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.2') ?>, -2.6em 0em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.2') ?>, -1.8em -1.8em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.2') ?>;
              }
              50% {
                box-shadow: 0em -2.6em 0em 0em <?php echo mg_static::hex2rgba($lb_color, '.2') ?>, 1.8em -1.8em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.2') ?>, 2.5em 0em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.5') ?>, 1.75em 1.75em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.7') ?>, 0em 2.5em 0 0em <?php echo $lb_color ?>, -1.8em 1.8em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.2') ?>, -2.6em 0em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.2') ?>, -1.8em -1.8em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.2') ?>;
              }
              62.5% {
                box-shadow: 0em -2.6em 0em 0em <?php echo mg_static::hex2rgba($lb_color, '.2') ?>, 1.8em -1.8em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.2') ?>, 2.5em 0em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.2') ?>, 1.75em 1.75em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.5') ?>, 0em 2.5em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.7') ?>, -1.8em 1.8em 0 0em <?php echo $lb_color ?>, -2.6em 0em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.2') ?>, -1.8em -1.8em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.2') ?>;
              }
              75% {
                box-shadow: 0em -2.6em 0em 0em <?php echo mg_static::hex2rgba($lb_color, '.2') ?>, 1.8em -1.8em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.2') ?>, 2.5em 0em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.2') ?>, 1.75em 1.75em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.2') ?>, 0em 2.5em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.5') ?>, -1.8em 1.8em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.7') ?>, -2.6em 0em 0 0em <?php echo $lb_color ?>, -1.8em -1.8em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.2') ?>;
              }
              87.5% {
                box-shadow: 0em -2.6em 0em 0em <?php echo mg_static::hex2rgba($lb_color, '.2') ?>, 1.8em -1.8em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.2') ?>, 2.5em 0em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.2') ?>, 1.75em 1.75em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.2') ?>, 0em 2.5em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.2') ?>, -1.8em 1.8em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.5') ?>, -2.6em 0em 0 0em <?php echo mg_static::hex2rgba($lb_color, '.7') ?>, -1.8em -1.8em 0 0em <?php echo $lb_color ?>;
              }
            }
			<?php
			break;
			
			
			
		/* bubbling dots */
		case 'bubbling_dots':
			?>
            .mg_loader div {
                background-color: <?php echo $color ?>;
            }
            #mg_lb_wrap .mg_loader div {
                background-color: <?php echo $lb_color ?>;
            }
            .mg_loader {
                -webkit-transform: scale(1.4);
                      transform: scale(1.4);
            }
            #mg_lb_loader .mg_loader {
            	-webkit-transform: scale(1);
                      transform: scale(1);
            }
            .mgl_1, .mgl_2, .mgl_3 {
                border-radius: 35px;
                bottom: -8px;
                display: inline-block;
                height: 6px;
                margin: 0 2px 0 0;
                position: relative;
                width: 6px;
                
                -webkit-animation:	mg_bubbling ease .65s infinite alternate;	
                animation: 			mg_bubbling ease .65s infinite alternate;
            }
            .mgl_2 {
                -webkit-animation-delay: 0.212s;
                animation-delay: 0.212s;
            }
            .mgl_3 {
                margin-right: 0;
                -webkit-animation-delay: 0.425s;
                animation-delay: 0.425s;
            }
            @-webkit-keyframes mg_bubbling {
                0% 		{-webkit-transform: scale(1) translateY(0);}
                35%		{opacity: 1;}
                100% 	{-webkit-transform: scale(1.3) translateY(-15px); opacity: .3;}
            }
            @keyframes mg_bubbling {
                0% 		{transform: scale(1) translateY(0);}
                35%		{opacity: 1;}
                100% 	{transform: scale(1.3) translateY(-15px); opacity: .3;}
            }
			<?php
			break;	
			
			
		
		/* overlapping dots */
		case 'overlapping_dots':
			?>
            .mg_loader div:before,
            .mg_loader div:after {
                background-color: <?php echo $color ?>;
            }
            #mg_lb_wrap .mg_loader div:before,
            #mg_lb_wrap .mg_loader div:after {
                background-color: <?php echo $lb_color ?>;
            }
            #mg_lb_loader .mg_loader {
            	-webkit-transform: scale(0.85);
                      	transform: scale(0.85);
            }
            .mgl_1 {
                width: 100%;
                height: 100%;
                border-radius: 50%;
                position: relative;
                vertical-align: middle;
                
                -webkit-animation: mg_overlap_dots1 1.73s infinite linear;
                animation: mg_overlap_dots1 1.73s infinite linear;
            }
            .mgl_1:before,
            .mgl_1:after {
                content:"";
                margin: -14px 0 0 -14px;
                width: 100%; 
                height: 100%;
                border-radius: 50%;
                position: absolute;
                top: 50%;
                left: 50%;
                
                -webkit-animation: mg_overlap_dots2 1.15s infinite ease-in-out;
                animation: mg_overlap_dots2 1.15s infinite ease-in-out;
            }
            .mgl_1:after { 
                -webkit-animation-direction: reverse;
                animation-direction: reverse;
            }
            
            @-webkit-keyframes mg_overlap_dots1 {
                0% {	-webkit-transform: rotate(0deg); }
                100% { -webkit-transform: rotate(360deg); }
            }
            @keyframes mg_overlap_dots1 {
                0% {	 transform: rotate(0deg); }
                100% { transform: rotate(360deg); }
            }
            
            @-webkit-keyframes mg_overlap_dots2 {
                0%	 { -webkit-transform: scale(0.2); left:	 0%; }
                50%	{ -webkit-transform: scale(1.0); left:	50%; }
                100% { -webkit-transform: scale(0.2); left: 100%; opacity: 0.5; }
            }
            @keyframes mg_overlap_dots2 {
                0%	 { transform: scale(0.2); left:	 0%; }
                50%	{ transform: scale(1.0); left:	50%; }
                100% { transform: scale(0.2); left: 100%; opacity: 0.5; }
            }
			<?php
			break;
			
			
			
		/* fading circles */
		case 'fading_circles':
			?>
            .mg_loader div:before,
            .mg_loader div:after {
                background-color: <?php echo $color ?>;
            }
            #mg_lb_wrap .mg_loader div:before,
            #mg_lb_wrap .mg_loader div:after {
                background-color: <?php echo $lb_color ?>;
            }
            .mgl_1 {
                width: 100%;
                height: 100%;
                border-radius: 50%;
                position: relative;
                vertical-align: middle;
            }
            #mg_lb_loader .mgl_1 {
                -webkit-transform: scale(0.85);
                transform: scale(0.85);	
            }
            .mgl_1:before,
            .mgl_1:after {
                content: "";
                width: 100%; 
                height: 100%;
                border-radius: 50%;
                position: absolute;
                top: 0;
                left: 0;
                
                -webkit-transform: scale(0);
                transform: scale(0);
            
                -webkit-animation: 	mg_fading_circles 1.4s infinite ease-in-out;
                animation: 			mg_fading_circles 1.4s infinite ease-in-out;
            }
            .mgl_1:after { 
                -webkit-animation-delay: 0.7s;
                animation-delay: 0.7s;
            }
            @-webkit-keyframes mg_fading_circles {
                0%	 { -webkit-transform: translateX(-80%) scale(0); }
                50%	{ -webkit-transform: translateX(0)		scale(1); }
                100% { -webkit-transform: translateX(80%)	scale(0); }
            }
            @keyframes mg_fading_circles {
                0%	 { transform: translateX(-80%) scale(0); }
                50%	{ transform: translateX(0)		scale(1); }
                100% { transform: translateX(80%)	scale(0); }
            }
			<?php
			break;
		
	}
}
