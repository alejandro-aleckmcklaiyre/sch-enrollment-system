<script type="text/php">
if (isset($pdf)) {
    $font = $fontMetrics->getFont("Helvetica", "normal");
    $size = 9;
    $date = date(config('export.date_format'));
    $pdf->page_script(<<<'PHP'
        $font = $fontMetrics->getFont("Helvetica", "normal");
        $size = 9;
        $text = "Page " . $PAGE_NUM . " of " . $PAGE_COUNT . " — Printed on: {$date}";
        $y = $pdf->get_height() - 24;
        $x = 40;
        $pdf->text($x, $y, $text, $font, $size);
    PHP
    );
}
</script>
