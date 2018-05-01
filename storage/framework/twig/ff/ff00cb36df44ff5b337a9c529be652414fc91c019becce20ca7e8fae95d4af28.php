<?php

/* layout.html.twig */
class __TwigTemplate_87706d38e9979f7a264403fd5f855b3d940e5b5cb76d5696ede0ff5def7ff044 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = false;

        $this->blocks = array(
            'container' => array($this, 'block_container'),
        );
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 1
        echo "<html lang=\"en\">
<head>
    <meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\">

    <title>测试程序</title>

    <link href=\"/bundle/HomeBundle/css/bootstrap.min.css\" rel=\"stylesheet\">
</head>

<body class=\"index-body\">
<div id=\"container\">
    ";
        // line 12
        $this->displayBlock('container', $context, $blocks);
        // line 14
        echo "</div>

</body>
</html>";
    }

    // line 12
    public function block_container($context, array $blocks = array())
    {
        // line 13
        echo "    ";
    }

    public function getTemplateName()
    {
        return "layout.html.twig";
    }

    public function getDebugInfo()
    {
        return array (  45 => 13,  42 => 12,  35 => 14,  33 => 12,  20 => 1,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Twig_Source("<html lang=\"en\">
<head>
    <meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\">

    <title>测试程序</title>

    <link href=\"/bundle/HomeBundle/css/bootstrap.min.css\" rel=\"stylesheet\">
</head>

<body class=\"index-body\">
<div id=\"container\">
    {% block container %}
    {% endblock %}
</div>

</body>
</html>", "layout.html.twig", "D:\\xampp_7.0\\htdocs\\Trochilidae\\src\\HomeBundle\\Resources\\views\\layout.html.twig");
    }
}
