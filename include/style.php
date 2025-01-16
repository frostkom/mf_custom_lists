<?php

if(!defined('ABSPATH'))
{
	header("Content-Type: text/css; charset=utf-8");

	$folder = str_replace("/wp-content/plugins/mf_custom_lists/include", "/", dirname(__FILE__));

	require_once($folder."wp-load.php");
}

$column_gap = 5;

echo "@media all
{
	.custom_list
	{
		list-style: none;
		padding-left: 0;
	}

		.custom_list li + li
		{
			margin-top: 0;
		}";

		/*.custom_list.list_alternate li
		{
			display: flex;
		}

			.custom_list.list_alternate li > *
			{
				flex: 1 1 auto;
			}

			.custom_list.list_alternate li:nth-child(2n) > *:first-child, .custom_list.list_alternate li:nth-child(2n + 1) > *:last-child
			{
				order: 1;
			}

			.custom_list.list_alternate li:nth-child(2n + 1) > *:first-child, .custom_list.list_alternate li:nth-child(2n) > *:last-child
			{
				order: 2;
			}*/

	echo "/* Style */
	.custom_list h2, .custom_list h3
	{
		overflow: hidden;
		text-overflow: ellipsis;
	}

	.custom_list .image
	{
		display: none;
		margin-bottom: .5em;
	}

		.custom_list.custom_list_has_image .image
		{
			display: block;
		}";

	/*echo ".is_mobile .custom_list_columns_mobile_1 > li + li
	{
		margin-top: 3em;
	}";*/

	echo "/* Style-Specific */
	.custom_list_style_logos, .custom_list_style_screenshots, .custom_list_style_people, .custom_list_style_testimonials /*.custom_list_style_about_us, .custom_list_style_vertical, .custom_list_style_horizontal, .custom_list_style_flex*/
	{
		display: flex;
		flex-wrap: wrap;
	}

		.custom_list_style_logos > li, .custom_list_style_screenshots > li, .custom_list_style_people > li, .custom_list_style_testimonials > li /*.custom_list_style_about_us > li, .custom_list_style_vertical > li, .custom_list_style_horizontal > li, .custom_list_style_flex > li*/
		{
			flex: 0 1 auto;
		}";

	/* About Us */
	/*echo ".custom_list_style_about_us > li
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
			transform: scale3d(1.9, 1.4, 1) rotate3d(0, 0, 1, 45deg) translate3d(0, -100%, 0);
			z-index: 1;
		}

			.custom_list_style_about_us > li:hover:before
			{
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
				transform: rotate(-2deg);
			}

		.custom_list_style_about_us > li:nth-child(2n+1) img
		{
			border-radius: 59% 52% 59% 56%;
			transform: rotate(-6deg);
		}

			.custom_list_style_about_us > li:nth-child(2n+1) img:hover
			{
				border-radius: 51% 67% 64% 56%;
				transform: rotate(-4deg);
			}

		.custom_list_style_about_us > li:nth-child(3n+2) img
		{
			border-radius: 84% 94% 72% 83%;
			transform: rotate(5deg);
		}

			.custom_list_style_about_us > li:nth-child(3n+2) img:hover
			{
				border-radius: 69% 64% 70% 53%;
				transform: rotate(1deg);
			}

		.custom_list_style_about_us > li:nth-child(5n+3) img
		{
			border-radius: 73% 100% 100% 82%;
			transform: rotate(7deg);
		}

			.custom_list_style_about_us > li:nth-child(5n+3) img:hover
			{
				border-radius: 73% 70% 80% 82%;
				transform: rotate(6deg);
			}

		.custom_list_style_about_us > li:nth-child(7n+5) img
		{
			border-radius: 93% 90% 78% 85%;
			transform: rotate(-2deg);
		}

			.custom_list_style_about_us > li:nth-child(7n+5) img:hover
			{
				border-radius: 53% 70% 68% 85%;
				transform: rotate(-2deg);
			}

		.custom_list_style_about_us > li:nth-child(11n+7) img
		{
			border-radius: 68% 68% 53% 83%;
			transform: rotate(-5deg);
		}

			.custom_list_style_about_us > li:nth-child(11n+7) img:hover
			{
				border-radius: 58% 98% 83% 78%;
				transform: rotate(3deg);
			}";*/

	/* Flags */
	/*echo "header .custom_list_style_flags
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
		}";*/

	/* Flex */
	/*echo ".custom_list_style_flex > li
	{
		text-align: center;
	}";*/

	/* Horizontal */
	/*echo ".custom_list_style_horizontal > li
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
				}";*/

	echo "/* Logos */
	.custom_list_style_logos .image
	{
		height: 100%;
	}

		.custom_list_style_logos li img
		{
			display: block;
			filter: gray;
			filter: grayscale(1);
			height: 100%;
			margin: 0 auto;
			object-fit: contain;
		}

			.custom_list_style_logos li img:hover
			{
				filter: none;
				filter: grayscale(0);
			}";

	/* Logos v2 */
	/*echo ".custom_list_style_logos_v2
	{
		display: flex;
		flex-wrap: wrap;
		justify-content: center;
	}

		.custom_list_style_logos_v2 > li
		{
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
				}";*/

	/* One Column */
	/*echo ".custom_list_style_one_col > li
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
		}";*/

	echo "/* People */
	.custom_list_style_people > li
	{
		text-align: center;
	}

		.custom_list_style_people .image
		{
			/*background: #f3f3f3;*/
			height: 0;
			padding-bottom: 100%;
			position: relative;
			overflow: hidden;
		}

			.custom_list_style_people .image img
			{
				border-radius: 50%;
				height: 100%;
				left: 0;
				margin: 0 auto;
				object-fit: cover;
				position: absolute;
				width: 100%;
			}

	/* Screenshots */
	.custom_list_style_screenshots > li
	{
		border: .3em solid #999;
		border-radius: .5em;
		overflow: hidden;
		position: relative;
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
			transform: scale3d(1.9, 1.4, 1) rotate3d(0, 0, 1, 45deg) translate3d(0, -100%, 0);
		}

			.custom_list_style_screenshots > li:hover:before
			{
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
			transform: translate3d(0, 100%, 0);
		}

			#wrapper .custom_list_style_screenshots li:hover h2, #wrapper .custom_list_style_screenshots li:hover h4
			{
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
				transform: translate3d(0, 100%, 0);
			}

				.custom_list_style_screenshots li:hover h2 a, .custom_list_style_screenshots li:hover h4 a
				{
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
			}";

	/* Slider */
	/*echo ".custom_list_style_slider
	{
		display: flex;
		text-align: center;
	}

		.custom_list_style_slider > li
		{
			flex: 1 1 auto;
		}";*/

	echo "/* Testimonials */
	.custom_list_style_testimonials .image
	{
		background: #f3f3f3;
		border-radius: 50%;
		height: 0;
		padding-bottom: 100%;
		position: relative;
		overflow: hidden;
	}

		.custom_list_style_testimonials .image img
		{
			height: 100%;
			left: 0;
			margin: 0 auto;
			object-fit: cover;
			position: absolute;
			width: 100%;
		}";

	/* Vertical */
	/*echo ".custom_list_style_vertical > li
	{
		text-align: center;
	}";*/

echo "}";