<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0" type="text/xsl" href="meteo.xsl">
	<xsl:output method="html" version="4.0" encoding="UTF-8" indent="yes" media-type="text/html"/>

	<xsl:template match="/">
		<xsl:apply-templates select='previsions/echeance[@hour = 6]'/>
		<xsl:apply-templates select='previsions/echeance[@hour = 12]'/>
		<xsl:apply-templates select='previsions/echeance[@hour = 18]'/>
	</xsl:template>

	<xsl:template match='previsions/echeance'>
		
		<div class='item'>
			<!-- Avancement du jour -->
			<xsl:choose>
				<xsl:when test="@hour = 6">
				<h3>Matin</h3>
				</xsl:when>
				<xsl:when test="@hour = 12">
				<h3>Midi</h3>
				</xsl:when>
				<xsl:when test="@hour = 18">
				<h3>Soir</h3>
				</xsl:when>
			</xsl:choose> 

			<div class="images">
				<div class="left">
					<p><i class="fas fa-thermometer-three-quarters icon fa-2x"></i>	<xsl:value-of select='round(temperature/level[@val = "sol"] - 273 )'/> °C</p>
					<p><i class="fas fa-wind icon fa-2x"></i><xsl:value-of select='vent_moyen'/> km/h</p>
				</div>
				<div class="right">
					<xsl:choose>
					<xsl:when test="pluie > 0.5">
							<i class="fas fa-cloud-showers-heavy fa-5x"></i>
					</xsl:when>

					<xsl:otherwise>
						<xsl:choose>
							<xsl:when test="nebulosite/level[@basse] > 50">
									<i class="fas fa-cloud fa-5x"></i>
							</xsl:when>

							<xsl:otherwise>
								<i class="fas fa-sun fa-5x"></i>
							</xsl:otherwise>
						</xsl:choose>
					</xsl:otherwise>
				</xsl:choose> 
				</div>
			</div>
			
			<!-- Images -->
			

		</div>

	</xsl:template>



</xsl:stylesheet>
