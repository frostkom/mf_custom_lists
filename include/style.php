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

	echo "/* Style */";

	/*echo ".custom_list h2, .custom_list h3
	{
		overflow: hidden;
		text-overflow: ellipsis;
	}";*/

	echo ".custom_list .image
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
	.custom_list_style_logos, .custom_list_style_screenshots, .custom_list_style_people, .custom_list_style_testimonials
	{
		display: flex;
		flex-wrap: wrap;
	}

		.custom_list_style_logos > li, .custom_list_style_screenshots > li, .custom_list_style_people > li, .custom_list_style_testimonials > li
		{
			flex: 0 1 auto;
		}

	/* FAQ */
	.custom_list_style_faq
	{
		margin-top: 0;
	}

		.custom_list_style_faq > li
		{
			border: .1em solid #e2e2e2;
			border-radius: .3em;
			margin-bottom: 1em;
			overflow: hidden;
		}

			.custom_list_style_faq > li h4
			{
				background: #eee;
				margin: 0;
				padding: .8rem 1rem;
			}

				.custom_list_style_faq > li h4 .fa
				{
					font-size: .8rem;
					margin-right: .5rem;
					vertical-align: top;
					margin-top: .5rem;
				}

				.custom_list_style_faq > li h4 a
				{
					text-decoration: none;
				}

			.custom_list_style_faq > li p, .custom_list_style_faq > li ul
			{
				background: #fff;
				display: none;
				margin: 0;
				padding: .8rem 1rem;
			}

				.custom_list_style_faq > li ul
				{
					list-style-position: inside;
				}

				.custom_list_style_faq > li p + p, .custom_list_style_faq > li p + ul, .custom_list_style_faq > li ul + p, .custom_list_style_faq > li ul + ul
				{
					padding-top: 0;
				}

	/* Logos */
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
			}

	/* People */
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
			}

	/* Testimonials */
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
		}
}";