<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0" type="text/xsl" href="meteo_nancy.xsl">
	<xsl:output method="html" version="4.0" encoding="UTF-8" indent="yes" media-type="text/html"/>

	<xsl:template match="/">
		<xsl:apply-templates select='previsions/echeance[@hour = 6]'/>
		<xsl:apply-templates select='previsions/echeance[@hour = 12]'/>
		<xsl:apply-templates select='previsions/echeance[@hour = 18]'/>
	</xsl:template>

	<xsl:template match='previsions/echeance'>
		
		<div>
			<!-- Avancement du jour -->
			<xsl:choose>
				<xsl:when test="@hour = 6">
				Matin
				</xsl:when>
				<xsl:when test="@hour = 12">
				Midi
				</xsl:when>
				<xsl:when test="@hour = 18">
				Soir
				</xsl:when>
			</xsl:choose> 

			<p>temperature : <xsl:value-of select='round(temperature/level[@val = "sol"] - 273 )'/> °C</p>
			
			<!-- Images -->
			<xsl:choose>
				<xsl:when test="pluie > 0.5">
					<div>
						<i class="fas fa-cloud-showers-heavy"></i>
					</div>
				</xsl:when>

				<xsl:otherwise>
					<xsl:choose>
						<xsl:when test="nebulosite/level[@basse] > 50">
							<div>
								<i class="fas fa-cloud"></i>
							</div>
						</xsl:when>

						<xsl:otherwise>
							<div><i class="fas fa-sun"></i></div>
						</xsl:otherwise>
					</xsl:choose>
				</xsl:otherwise>
			</xsl:choose> 

			<p>Vent : <xsl:value-of select='vent_moyen'/> km/h</p>
			<hr></hr>
		
		</div>

	</xsl:template>



</xsl:stylesheet>
