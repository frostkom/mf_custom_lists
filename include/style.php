<?php

if(!defined('ABSPATH'))
{
	header("Content-Type: text/css; charset=utf-8");

	$folder = str_replace("/wp-content/plugins/mf_custom_lists/include", "/", dirname(__FILE__));

	require_once($folder."wp-load.php");
}

echo "@media all
{
	.custom_list
	{
		list-style: none;
		padding-left: 0;
	}

		.list_alternate li
		{
			display: -webkit-box;
			display: -ms-flexbox;
			display: -webkit-flex;
			display: flex;
		}

			.list_alternate li > *
			{
				-webkit-box-flex: 1 1 auto;
				-webkit-flex: 1 1 auto;
				-ms-flex: 1 1 auto;
				flex: 1 1 auto;
			}

			.list_alternate li:nth-child(2n) > *:first-child, .list_alternate li:nth-child(2n + 1) > *:last-child
			{
				-webkit-box-ordinal-group: 1;
				-webkit-order: 1;
				-ms-flex-order: 1;
				order: 1;
			}

			.list_alternate li:nth-child(2n + 1) > *:first-child, .list_alternate li:nth-child(2n) > *:last-child
			{
				-webkit-box-ordinal-group: 2;
				-webkit-order: 2;
				-ms-flex-order: 2;
				order: 2;
			}

	/* Style */
	.custom_list h2, .custom_list h3
	{
		overflow: hidden;
		text-overflow: ellipsis;
	}

		/* This messes up widget heading styles in aside */
		/*.custom_list h2
		{
			padding: 0 0 .5em !important;
		}*/

	.custom_list .image
	{
		display: none;
		margin-bottom: .5em;
	}

		.custom_list.custom_list_has_image .image
		{
			display: block;
		}

	/* Columns */
	.custom_list_columns_desktop_5 li
	{
		width: 19%;
	}

	.custom_list_columns_desktop_4 li
	{
		width: 24%;
	}

	.custom_list_columns_desktop_3 li, .is_tablet .custom_list_columns_tablet_3 li
	{
		width: 32%;
	}

	.custom_list_columns_desktop_2 li, .is_tablet .custom_list_columns_tablet_2 li, .is_mobile .custom_list_columns_mobile_2 li
	{
		width: 49%;
	}

	.custom_list_columns_desktop_1 li, .is_tablet .custom_list_columns_tablet_1 li, .is_mobile .custom_list_columns_mobile_1 li
	{
		width: 99%;
	}

		.is_mobile .custom_list_columns_mobile_1 > li + li
		{
			margin-top: 3em;
		}

	/* Style-Specific */
	article .custom_list_style_vertical, article .custom_list_style_horizontal, article .custom_list_style_about_us, article .custom_list_style_flex, article .custom_list_style_logos, article .custom_list_style_screenshots, .custom_list_style_people, .custom_list_style_testimonials
	{
		display: -webkit-box;
		display: -ms-flexbox;
		display: -webkit-flex;
		display: flex;
		-webkit-box-flex-wrap: wrap;
		-webkit-flex-wrap: wrap;
		-ms-flex-wrap: wrap;
		flex-wrap: wrap;
	}

		article .custom_list_style_vertical > li, article .custom_list_style_horizontal > li, article .custom_list_style_about_us > li, article .custom_list_style_flex > li, article .custom_list_style_logos > li, article .custom_list_style_screenshots > li, .custom_list_style_people > li, .custom_list_style_testimonials > li
		{
			-webkit-box-flex: 0 1 auto;
			-webkit-flex: 0 1 auto;
			-ms-flex: 0 1 auto;
			flex: 0 1 auto;
			margin: 0 .5% .5em;
		}

	/* Vertical */
	.custom_list_style_vertical > li
	{
		text-align: center;
	}

	/* Horizontal */
	.custom_list_style_horizontal > li
	{
		overflow: hidden;
	}

		.custom_list_style_horizontal.custom_list_has_image .image
		{
			float: left;
			width: 30%;
		}

		.custom_list_style_horizontal.custom_list_has_image h2, .custom_list_style_horizontal.custom_list_has_image h3, .custom_list_style_horizontal.custom_list_has_image h4, .custom_list_style_horizontal.custom_list_has_image p
		{
			float: right;
			width: 65%;
		}

		.is_mobile .custom_list_style_horizontal.custom_list_has_image li > *
		{
			float: none;
			width: 100%;
		}

			.is_mobile .custom_list_style_horizontal .image
			{
				text-align: center;
			}

				.is_mobile .custom_list_style_horizontal .image img
				{
					max-width: 75%;
				}

	/* About Us */
	.custom_list_style_about_us > li
	{
		border: 0;
		border-radius: .5em;
		overflow: hidden;
		position: relative;
		text-align: center;
	}

		article .custom_list_style_about_us > li
		{
			width: 23%;
		}

			.is_mobile article .custom_list_style_about_us > li
			{
				width: 48%;
			}

		.custom_list_style_about_us > li:before
		{
			position: absolute;
			top: 0;
			left: 0;
			width: 100%;
			height: 100%;
			background: rgba(255, 255, 255, .5);
			content: '';
			transition: transform .5s;
			-webkit-transform: scale3d(1.9, 1.4, 1) rotate3d(0, 0, 1, 45deg) translate3d(0, -100%, 0);
			transform: scale3d(1.9, 1.4, 1) rotate3d(0, 0, 1, 45deg) translate3d(0, -100%, 0);
			z-index: 1;
		}

			.custom_list_style_about_us > li:hover:before
			{
				-webkit-transform: scale3d(1.9, 1.4, 1) rotate3d(0, 0, 1, 45deg) translate3d(0, 100%, 0);
				transform: scale3d(1.9, 1.4, 1) rotate3d(0, 0, 1, 45deg) translate3d(0, 100%, 0);
			}

		.custom_list_style_about_us > li img
		{
			display: block;
			margin: 0 auto 1em;
			border-radius: 87% 91% 100% 98%;
			transition: all 1s;
		}

			.custom_list_style_about_us > li img:hover
			{
				border-radius: 95% 70% 80% 100%;
				-webkit-transform: rotate(-2deg);
				transform: rotate(-2deg);
			}

		.custom_list_style_about_us > li:nth-child(2n+1) img
		{
			border-radius: 59% 52% 59% 56%;
			-webkit-transform: rotate(-6deg);
			transform: rotate(-6deg);
		}

			.custom_list_style_about_us > li:nth-child(2n+1) img:hover
			{
				border-radius: 51% 67% 64% 56%;
				-webkit-transform: rotate(-4deg);
				transform: rotate(-4deg);
			}

		.custom_list_style_about_us > li:nth-child(3n+2) img
		{
			border-radius: 84% 94% 72% 83%;
			-webkit-transform: rotate(5deg);
			transform: rotate(5deg);
		}

			.custom_list_style_about_us > li:nth-child(3n+2) img:hover
			{
				border-radius: 69% 64% 70% 53%;
				-webkit-transform: rotate(1deg);
				transform: rotate(1deg);
			}

		.custom_list_style_about_us > li:nth-child(5n+3) img
		{
			border-radius: 73% 100% 100% 82%;
			-webkit-transform: rotate(7deg);
			transform: rotate(7deg);
		}

			.custom_list_style_about_us > li:nth-child(5n+3) img:hover
			{
				border-radius: 73% 70% 80% 82%;
				-webkit-transform: rotate(6deg);
				transform: rotate(6deg);
			}

		.custom_list_style_about_us > li:nth-child(7n+5) img
		{
			border-radius: 93% 90% 78% 85%;
			-webkit-transform: rotate(-2deg);
			transform: rotate(-2deg);
		}

			.custom_list_style_about_us > li:nth-child(7n+5) img:hover
			{
				border-radius: 53% 70% 68% 85%;
				-webkit-transform: rotate(-2deg);
				transform: rotate(-2deg);
			}

		.custom_list_style_about_us > li:nth-child(11n+7) img
		{
			border-radius: 68% 68% 53% 83%;
			-webkit-transform: rotate(-5deg);
			transform: rotate(-5deg);
		}

			.custom_list_style_about_us > li:nth-child(11n+7) img:hover
			{
				border-radius: 58% 98% 83% 78%;
				-webkit-transform: rotate(3deg);
				transform: rotate(3deg);
			}

	/* Flags */
	header .custom_list_style_flags
	{
		float: right;
		margin: .9em .4em 0 1em;
	}

		.is_mobile header .custom_list_style_flags
		{
			display: none;
		}

		header .custom_list_style_flags > li
		{
			display: inline-block;
		}

	/* Flex */
	.custom_list_style_flex > li
	{
		text-align: center;
	}

	/* Logos */
	.custom_list_style_logos > li
	{
		border: .3em solid #999;
		border-radius: .5em;
		margin: 0 1% 1.2em 1%;
		overflow: hidden;
		padding: 1em;
	}

		article .custom_list_style_logos > li
		{
			width: 31%;
		}

			.is_mobile article .custom_list_style_logos > li
			{
				width: 48%;
			}

		.custom_list_style_logos li a
		{
			border-bottom: 0;
			display: block;
			text-decoration: none;
		}

			.custom_list_style_logos li .image
			{
				margin-bottom: 0;
			}

				.custom_list_style_logos img
				{
					display: block;
					filter: gray;
					-webkit-filter: grayscale(1);
					filter: grayscale(1);
					margin: 0 auto;
				}

					.custom_list_style_logos img:hover
					{
						filter: none;
						-webkit-filter: grayscale(0);
						filter: grayscale(0);
					}

	/* Logos v2 */
	.custom_list_style_logos_v2
	{
		display: -webkit-box;
		display: -ms-flexbox;
		display: -webkit-flex;
		display: flex;
		-webkit-box-flex-wrap: wrap;
		-webkit-flex-wrap: wrap;
		-ms-flex-wrap: wrap;
		flex-wrap: wrap;
		justify-content: center;
	}

		.custom_list_style_logos_v2 > li
		{
			-webkit-box-flex: 0 1 auto;
			-webkit-flex: 0 1 auto;
			-ms-flex: 0 1 auto;
			flex: 0 1 auto;
		}

			.custom_list_style_logos_v2 li .image
			{
				margin-bottom: 0;
			}

				.custom_list_style_logos_v2 img
				{
					display: block;
					margin: 0 auto;
				}

	/* One Column */
	.custom_list_style_one_col > li
	{
		overflow: hidden;
	}

		.custom_list_style_one_col .image
		{
			float: left;
			width: 30%;
		}

		.custom_list_style_one_col p
		{
			float: right;
			width: 65%;
		}

	/* People */
	.custom_list_style_people > li
	{
		text-align: center;
	}

		article .custom_list_style_people > li
		{
			width: 24%;
		}

			.is_tablet article .custom_list_style_people > li
			{
				width: 32%;
			}

			.is_mobile article .custom_list_style_people > li
			{
				width: 99%;
			}

				.custom_list_style_people .image
				{
					background: #f3f3f3;
					/*border-radius: 50%;*/
					height: 0;
					padding-bottom: 100%;
					position: relative;
					overflow: hidden;
				}

					.custom_list_style_people .image img
					{
						height: 100%;
						left: 0;
						margin: 0 auto;
						object-fit: contain;
						position: absolute;
						width: 100%;
					}

	/* Screenshots */
	.custom_list_style_screenshots > li
	{
		border: .3em solid #999;
		border-radius: .5em;
		margin: 0 1% 1.2em 1%;
		overflow: hidden;
		position: relative;
	}

		article .custom_list_style_screenshots > li
		{
			width: 48%;
		}

			.is_mobile article .custom_list_style_screenshots > li
			{
				width: 98%;
			}

		.custom_list_style_screenshots > li:before
		{
			position: absolute;
			top: 0;
			left: 0;
			width: 100%;
			height: 100%;
			background: rgba(255, 255, 255, .5);
			content: '';
			transition: transform .5s;
			-webkit-transform: scale3d(1.9, 1.4, 1) rotate3d(0, 0, 1, 45deg) translate3d(0, -100%, 0);
			transform: scale3d(1.9, 1.4, 1) rotate3d(0, 0, 1, 45deg) translate3d(0, -100%, 0);
		}

			.custom_list_style_screenshots > li:hover:before
			{
				-webkit-transform: scale3d(1.9, 1.4, 1) rotate3d(0, 0, 1, 45deg) translate3d(0, 100%, 0);
				transform: scale3d(1.9, 1.4, 1) rotate3d(0, 0, 1, 45deg) translate3d(0, 100%, 0);
			}

		#wrapper .custom_list_style_screenshots li h2, #wrapper.custom_list_style_screenshots li h4
		{
			bottom: 0;
			background: #fff;
			left: 0;
			margin-bottom: 0;
			padding: .8em 1em .3em;
			position: absolute;
			right: 0;
			text-align: left;
			transition: transform .5s ease;
			-webkit-transform: translate3d(0, 100%, 0);
			transform: translate3d(0, 100%, 0);
		}

			#wrapper .custom_list_style_screenshots li:hover h2, #wrapper .custom_list_style_screenshots li:hover h4
			{
				-webkit-transform: translate3d(0, 0, 0);
				transform: translate3d(0, 0, 0);
			}

			.custom_list_style_screenshots li h2 a, .custom_list_style_screenshots li h4 a
			{
				border-bottom: 0;
				color: inherit;
				float: right;
				font-size: .8em;
				line-height: 1.8;
				transition: transform 1.5s ease;
				-webkit-transform: translate3d(0, 100%, 0);
				transform: translate3d(0, 100%, 0);
			}

				.custom_list_style_screenshots li:hover h2 a, .custom_list_style_screenshots li:hover h4 a
				{
					-webkit-transform: translate3d(0, 0, 0);
					transform: translate3d(0, 0, 0);
				}

		.custom_list_style_screenshots li .image
		{
			margin-bottom: 0;
		}

			.custom_list_style_screenshots li .image img
			{
				display: block;
				margin: 0 auto;
			}

	/* Slider */
	.custom_list_style_slider
	{
		display: -webkit-box;
		display: -ms-flexbox;
		display: -webkit-flex;
		display: flex;
		text-align: center;
	}

		.custom_list_style_slider > li
		{
			-webkit-box-flex: 1 1 auto;
			-webkit-flex: 1 1 auto;
			-ms-flex: 1 1 auto;
			flex: 1 1 auto;
		}

	/* Testimonials */
	.custom_list_style_testimonials > li
	{
		box-shadow: none;
		border-radius: .5em;
		padding: 0 2em 2em;
		width: 24%;
		transition: all 4s ease;
	}

		.custom_list_style_testimonials > li:hover
		{
			box-shadow: 0 2em 2em rgba(0, 0, 0, .3);
		}

		.is_tablet .custom_list_style_testimonials > li
		{
			width: 32%;
		}

		.is_mobile .custom_list_style_testimonials > li
		{
			width: 99%;
		}

			.custom_list_style_testimonials li > p
			{
				margin: 2em 0;
			}

			.custom_list_style_testimonials h4
			{
				float: right;
				margin: .2em 0 0;
				width: 70%;
			}

			.custom_list_style_testimonials h4 + p
			{
				clear: right;
				float: right;
				margin: 0;
				width: 70%;
			}

			.custom_list_style_testimonials .image
			{
				float: left;
				margin-right: 5%;
				width: 25%;
			}
}";